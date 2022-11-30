<?php

namespace Training5\Vendor\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Training5\Vendor\Model\ResourceModel\Vendor\CollectionFactory $vendorCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $vendor = $setup->getTable('training4_vendor');
            $vendor_product = $setup->getTable('vendor_product');
            $product = $setup->getTable('catalog_product_entity');
            if ($setup->getConnection()->isTableExists($vendor) &&
                $setup->getConnection()->isTableExists($vendor_product) &&
                $setup->getConnection()->isTableExists($product)) {
                $productCollection = $this->productCollectionFactory->create();
                $vendorCollection = $this->vendorCollectionFactory->create();
                $productCount = $productCollection->count();
                $vendorCount = $vendorCollection->count();
                $data = [];
                for ($i = 1; $i <= $productCount; $i++) {
                    $vendor_id = rand(1, $vendorCount);
                    $data[] = [
                        'product_id' => $i,
                        'vendor_id' => $vendor_id
                    ];
                    if ($vendor_id == 1) {
                        $data[] = [
                            'product_id' => $i,
                            'vendor_id' => 2
                        ];
                    } elseif ($vendor_id == $vendorCount) {
                        $data[] = [
                            'product_id' => $i,
                            'vendor_id' => 1
                        ];
                    } else {
                        $data[] = [
                            'product_id' => $i,
                            'vendor_id' => $vendor_id - 1
                        ];
                        $data[] = [
                            'product_id' => $i,
                            'vendor_id' => $vendor_id + 1
                        ];
                    }
                }
                foreach ($data as $item) {
                    $setup->getConnection()->insert($vendor_product, $item);
                }
            }
        }
        $setup->endSetup();
    }
}
