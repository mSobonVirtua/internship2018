<?php
/**
 * VI-73 ProductCategoryImportCommand
 *
 * @category  Command
 * @package   Virtua_ProductCategoryImport
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Command;

use App\Services\FileLoggerService;
use App\Services\ProductCategoryService;
use App\Services\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ProductCategoryImportCommand
 */
class ProductCategoryImportCommand extends Command
{
    /**
     * @var SerializerService
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ProductCategoryService
     */
    private $productCategoryService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FileLoggerService
     */
    private $fileLogger;

    /**
     * ProductCategoryImportCommand constructor.
     * @param string $name
     * @param SerializerService $serializer
     * @param ValidatorInterface $validator
     * @param ProductCategoryService $productCategoryService
     * @param EntityManagerInterface $entityManager
     * @param FileLoggerService $fileLogger
     */
    public function __construct(
        SerializerService $serializer,
        ValidatorInterface $validator,
        ProductCategoryService $productCategoryService,
        EntityManagerInterface $entityManager,
        FileLoggerService $fileLogger,
        string $name = null
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->productCategoryService = $productCategoryService;
        $this->em = $entityManager;
        $this->fileLogger = $fileLogger;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('app:import-product-category')
            ->setDescription('Import product category from file to database')
            ->addArgument('fileName', InputArgument::REQUIRED, 'path to the file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $productCategoryNotImportedLog = [];
        $numberOfErrors = 0;
        $numberOfImportedCategories = 0;
        $path = $input->getArgument('fileName');
        $productCategoryArray = $this->serializer->decode(file_get_contents('exportedData/'.$path), 'csv');

        if (count($productCategoryArray) === 0) {
            $io->error('CSV file is empty');
        }

        if ($this->isOnlyOneRowOfData($productCategoryArray)) {
            $productCategoryArray = [$productCategoryArray];
        }

        $tmpProductCategories = [];
        foreach ($productCategoryArray as $productCategoryArr) {
            $productCategory = $this->productCategoryService
                ->createProductCategoryFromArrayByKeyValue($productCategoryArr);
            $imgPath = $productCategory->getMainImage();
            try {
                $productCategory->setMainImage(new File("public/uploads/images/" . $imgPath));
            } catch (\Exception $exception) {
                $productCategoryNotImportedLog[] = [
                    'name' => $productCategory->getName(),
                    'reason' => 'File not exist'
                ];
                $numberOfErrors++;
                continue;
            }
            if (count($this->validator->validate($productCategory)) != 0) {
                $productCategoryNotImportedLog[] = [
                    'name' => $productCategory->getName(),
                    'reason' => 'Data not valid'
                ];
                $numberOfErrors++;
                continue;
            }
            $productCategory->setMainImage($imgPath);
            $tmpProductCategories[] = $productCategory;
        }
        foreach ($tmpProductCategories as $productCategory) {
            try {
                $this->em->persist($productCategory);
                $this->em->flush();
                $numberOfImportedCategories++;
            } catch (\Exception $exception) {
                $numberOfErrors++;
                $productCategoryNotImportedLog[] = [
                    'name' => $productCategory->getName(),
                    'reason' => 'Database error'
                ];
                continue;
            }
        }
        if ($numberOfErrors === 0) {
            $io->success('Categories successfully imported');
        } else {
            $this->fileLogger
                ->logIntoFile(
                    'logs/commandImportErrors.txt',
                    $productCategoryNotImportedLog,
                    "NotImportedCategory"
                );
            $io->error(json_encode($productCategoryNotImportedLog));
        }
    }

    /**
     * @param array $productCategoriesArray
     * @return bool
     */
    private function isOnlyOneRowOfData(array $productCategoriesArray) : bool
    {
        try {
            return $productCategoriesArray['name'] != null;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
