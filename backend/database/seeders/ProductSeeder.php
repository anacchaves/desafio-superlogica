<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            $this->command->error('Test user not found. Run UserSeeder first.');

            return;
        }

        $products = [
            // Produtos ATIVOS (estoque > 0)
            [
                'name' => 'Notebook Dell Inspiron 15',
                'description' => 'Notebook Dell com processador Intel Core i5, 8GB RAM, 256GB SSD',
                'price' => 3500.00,
                'stock' => 15,
            ],
            [
                'name' => 'Mouse Logitech MX Master 3',
                'description' => 'Mouse ergonômico sem fio com sensor de alta precisão',
                'price' => 450.00,
                'stock' => 30,
            ],
            [
                'name' => 'Teclado Mecânico Keychron K2',
                'description' => 'Teclado mecânico wireless com switches Gateron Brown',
                'price' => 650.00,
                'stock' => 20,
            ],
            [
                'name' => 'Monitor LG UltraWide 29"',
                'description' => 'Monitor ultrawide 29 polegadas, resolução 2560x1080',
                'price' => 1800.00,
                'stock' => 8,
            ],
            [
                'name' => 'Webcam Logitech C920',
                'description' => 'Webcam Full HD 1080p com microfone integrado',
                'price' => 550.00,
                'stock' => 12,
            ],

            // Produtos INATIVOS (estoque = 0) - podem ser excluídos
            [
                'name' => 'Headset HyperX Cloud II',
                'description' => 'Headset gamer com som surround 7.1 virtual',
                'price' => 480.00,
                'stock' => 0,
            ],
            [
                'name' => 'SSD Samsung 970 EVO 1TB',
                'description' => 'SSD NVMe M.2 de alta velocidade',
                'price' => 800.00,
                'stock' => 0,
            ],
            [
                'name' => 'Cadeira Gamer DXRacer',
                'description' => 'Cadeira ergonômica para longas sessões',
                'price' => 1200.00,
                'stock' => 0,
            ],

            // Produtos com preços variados para testar validação de 30%
            [
                'name' => 'Mousepad Gamer Grande',
                'description' => 'Mousepad extended 90x40cm com base antiderrapante',
                'price' => 80.00,  // Pode variar de 56.00 a 104.00
                'stock' => 50,
            ],
            [
                'name' => 'Hub USB-C 7 em 1',
                'description' => 'Hub multiportas com HDMI, USB 3.0 e leitor de cartão',
                'price' => 250.00,  // Pode variar de 175.00 a 325.00
                'stock' => 25,
            ],
        ];

        $this->command->info('Creating sample products...');

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $activeCount = count(array_filter($products, fn ($p) => $p['stock'] > 0));
        $inactiveCount = count(array_filter($products, fn ($p) => $p['stock'] === 0));

        $this->command->info("Created {$activeCount} active products (stock > 0)");
        $this->command->info("Created {$inactiveCount} inactive products (stock = 0)");
        $this->command->info('Total: '.count($products).' products created successfully');
    }
}
