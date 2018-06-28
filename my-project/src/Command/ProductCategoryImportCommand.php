<?php

namespace App\Command;

use App\Entity\ProductCategory;
use App\Services\FileLoggerService;
use App\Services\ProductCategoryService;
use App\Services\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductCategoryImportCommand extends Command
{

    private $serializer;
    private $validator;
    private $productCategoryService;
    private $em;
    private $fileLogger;

    public function __construct($name = null, SerializerService $serializer,
                                ValidatorInterface $validator,
                                ProductCategoryService $productCategoryService,
                                EntityManagerInterface $entityManager,
                                FileLoggerService $fileLogger)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->productCategoryService = $productCategoryService;
        $this->em = $entityManager;
        $this->fileLogger = $fileLogger;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:import-product-category')
            ->setDescription('Import product category from file to database')
            ->addArgument('fileName', InputArgument::REQUIRED, 'path to the file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $productCategoryNotImportedLog = [];
        $numberOfErrors = 0;
        $numberOfImportedCategories = 0;
        $path = $input->getArgument('fileName');
        $productCategoryArray = $this->serializer->decode(file_get_contents('exportedData/'.$path), 'csv');

        if(count($productCategoryArray) === 0)
        {
            $io->error('CSV file is empty');
        }

        if($this->_isOnlyOneRowOfData($productCategoryArray))
        {
            $productCategoryArray = [$productCategoryArray];
        }

        $tmpProductCategories = [];
        foreach ($productCategoryArray as $productCategoryArr)
        {
            $productCategory = $this->productCategoryService->createProductCategoryFromArrayByKeyValue($productCategoryArr);
            $imgPath = $productCategory->getMainImage();
            try{
                $productCategory->setMainImage(new File("public/uploads/images/" . $imgPath));
            }catch(\Exception $exception){
                $productCategoryNotImportedLog[] = [
                    'name' => $productCategory->getName(),
                    'reason' => 'File not exist'
                ];
                $numberOfErrors++;
                continue;
            }
            if(count($this->validator->validate($productCategory)) != 0)
            {
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
        foreach ($tmpProductCategories as $productCategory)
        {
            try
            {
                $this->em->persist($productCategory);
                $this->em->flush();
                $numberOfImportedCategories++;
            }catch(\Exception $exception)
            {
                $numberOfErrors++;
                $productCategoryNotImportedLog[] = [
                    'name' => $productCategory->getName(),
                    'reason' => 'Database error'
                ];
                continue;
            }
        }
        if($numberOfErrors === 0)
        {
            $io->success('Categories successfully imported');
        }
        else
        {
            $this->fileLogger->logIntoFile('logs/commandImportErrors.txt', $productCategoryNotImportedLog, "NotImportedCategory");
            $io->error(json_encode($productCategoryNotImportedLog));
        }

    }

    private function _isOnlyOneRowOfData(array $productCategoriesArray) : bool
    {
        try
        {
            return $productCategoriesArray['name'] != null;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}
