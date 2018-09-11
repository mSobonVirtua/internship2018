<?php
/**
 * VI-73 ProductCategoryExportCommand
 *
 * @category  Command
 * @package   Virtua_ProductCategoryExport
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Command;

use App\Repository\ProductCategoryRepository;
use App\Services\SerializerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ProductCategoryExportCommand
 */
class ProductCategoryExportCommand extends Command
{
    /**
     * @var SerializerService
     */
    private $serializer;

    /**
     * @var ProductCategoryRepository
     */
    private $productCategoryRepository;

    /**
     * ProductCategoryExportCommand constructor.
     * @param string $name
     * @param SerializerService $serializer
     * @param ProductCategoryRepository $productCategoryRepository
     */
    public function __construct(
        SerializerService $serializer,
        ProductCategoryRepository $productCategoryRepository,
        string $name = null
    ) {
        $this->serializer = $serializer;
        $this->productCategoryRepository = $productCategoryRepository;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('app:export-product-category')
            ->setDescription('Exporting productCategory to file')
            ->addArgument('fileName', InputArgument::REQUIRED, 'path to file')
            ->addArgument("categoryID", InputArgument::OPTIONAL, "If is not given, all categories are exported");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('fileName');
        $categoryId = $input->getArgument('categoryID');
        if (is_null($categoryId)) {
            $data = $this->serializer->normalize(
                $this->productCategoryRepository->findAll(),
                'csv',
                [
                'groups' => ['ProductCategoryExport']
                ]
            );
            if (is_null($data)) {
                $io->error("Categories not found");
                die;
            }
            $stringCsv = $this->serializer->encode($data, 'csv');
            file_put_contents('exportedData/'.$path, $stringCsv);
            $io->success("Categories exported");
        } else {
            $productCategory = $this->productCategoryRepository->find($categoryId);
            if (is_null($productCategory)) {
                $io->error("Category not found");
                die;
            }
            $data = $this->serializer->normalize(
                $productCategory,
                'csv',
                [
                'groups' => ['ProductCategoryExport']
                ]
            );
            $stringCsv = $this->serializer->encode($data, 'csv');
            file_put_contents('exportedData/'.$path, $stringCsv);
            $io->success('Category exported');
        }
    }
}
