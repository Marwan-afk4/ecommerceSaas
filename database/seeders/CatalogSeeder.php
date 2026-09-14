<?php

namespace Database\Seeders;

use App\Actions\Catalog\GenerateProductVariants;
use App\Enums\ProductStatus;
use App\Enums\ShopRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $platformAdmin = User::query()->create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_platform_admin' => true,
        ]);

        $this->seedPhoneShop($platformAdmin);
        $this->seedApparelShop($platformAdmin);
        $this->seedLaptopShop($platformAdmin);
    }

    private function seedPhoneShop(User $platformAdmin): void
    {
        $owner = User::query()->create([
            'name' => 'Pixel Owner',
            'email' => 'phones@example.com',
            'password' => Hash::make('password'),
        ]);

        $shop = Shop::query()->create([
            'name' => 'Pixel Phones',
            'slug' => 'pixel-phones',
            'tagline' => 'Phones that just work',
            'description' => 'A demo phone store.',
            'public_owner_name' => $owner->name,
            'public_email' => $owner->email,
            'is_active' => true,
        ]);

        $shop->addMember($owner, ShopRole::Owner);
        $shop->addMember($platformAdmin, ShopRole::Manager);

        $storage = $this->attribute($shop, 'Storage', ['128GB', '256GB']);
        $color = $this->attribute($shop, 'Color', [
            'Black' => '#111827',
            'Blue' => '#2563EB',
        ]);

        $category = $this->category($shop, 'Phones', [$storage, $color]);
        $product = $this->product($shop, $category, 'Pixel 15');

        app(GenerateProductVariants::class)->handle($product, [
            $storage->id => $storage->values->pluck('id')->all(),
            $color->id => $color->values->pluck('id')->all(),
        ]);
    }

    private function seedApparelShop(User $platformAdmin): void
    {
        $owner = User::query()->create([
            'name' => 'Apparel Owner',
            'email' => 'apparel@example.com',
            'password' => Hash::make('password'),
        ]);

        $shop = Shop::query()->create([
            'name' => 'Northwind Apparel',
            'slug' => 'northwind-apparel',
            'tagline' => 'Everyday clothes',
            'description' => 'A demo apparel store.',
            'public_owner_name' => $owner->name,
            'public_email' => $owner->email,
            'is_active' => true,
        ]);

        $shop->addMember($owner, ShopRole::Owner);

        $size = $this->attribute($shop, 'Size', ['S', 'M', 'L']);
        $color = $this->attribute($shop, 'Color', [
            'Red' => '#DC2626',
            'Black' => '#111827',
        ]);

        $category = $this->category($shop, 'T-Shirts', [$size, $color]);
        $product = $this->product($shop, $category, 'Classic Tee');

        app(GenerateProductVariants::class)->handle($product, [
            $size->id => $size->values->pluck('id')->all(),
            $color->id => $color->values->pluck('id')->all(),
        ]);
    }

    private function seedLaptopShop(User $platformAdmin): void
    {
        $owner = User::query()->create([
            'name' => 'Laptop Owner',
            'email' => 'laptops@example.com',
            'password' => Hash::make('password'),
        ]);

        $shop = Shop::query()->create([
            'name' => 'Byte Laptops',
            'slug' => 'byte-laptops',
            'tagline' => 'Laptops for work',
            'description' => 'A demo laptop store.',
            'public_owner_name' => $owner->name,
            'public_email' => $owner->email,
            'is_active' => true,
        ]);

        $shop->addMember($owner, ShopRole::Owner);

        $ram = $this->attribute($shop, 'RAM', ['16GB', '32GB']);
        $storage = $this->attribute($shop, 'Storage', ['512GB', '1TB']);

        $category = $this->category($shop, 'Notebooks', [$ram, $storage]);
        $product = $this->product($shop, $category, 'ProBook 14');

        app(GenerateProductVariants::class)->handle($product, [
            $ram->id => $ram->values->pluck('id')->all(),
            $storage->id => $storage->values->pluck('id')->all(),
        ]);
    }

    /**
     * @param  list<string>|array<string, string>  $values
     */
    private function attribute(Shop $shop, string $name, array $values): VariantAttribute
    {
        $attribute = VariantAttribute::query()->create([
            'shop_id' => $shop->id,
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        $sortOrder = 0;

        foreach ($values as $key => $value) {
            $isNamedColor = is_string($key);

            VariantAttributeValue::query()->create([
                'shop_id' => $shop->id,
                'variant_attribute_id' => $attribute->id,
                'name' => $isNamedColor ? $key : $value,
                'slug' => Str::slug($isNamedColor ? $key : $value),
                'hex' => $isNamedColor ? $value : null,
                'sort_order' => $sortOrder,
            ]);

            $sortOrder++;
        }

        return $attribute->load('values');
    }

    /**
     * @param  list<VariantAttribute>  $attributes
     */
    private function category(Shop $shop, string $name, array $attributes): Category
    {
        $category = Category::query()->create([
            'shop_id' => $shop->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ]);

        foreach ($attributes as $sortOrder => $attribute) {
            $category->variantAttributes()->attach($attribute->id, [
                'is_required' => true,
                'sort_order' => $sortOrder,
            ]);
        }

        return $category->load('variantAttributes');
    }

    private function product(Shop $shop, Category $category, string $name): Product
    {
        return Product::query()->create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => ProductStatus::Active,
        ]);
    }
}
