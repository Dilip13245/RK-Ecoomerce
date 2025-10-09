<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function categoryList(Request $request)
    {
        try {
            $categories = Category::active()
                ->select('id', 'name', 'image')
                ->orderBy('id', 'ASC')
                ->get();

            if ($categories->isEmpty()) {
                return $this->toJsonEnc([], 'No categories found', 404);
            }

            $result = [];
            foreach ($categories as $category) {
                $imageUrl = $category->image ? asset('storage/categories/' . $category->image) : null;

                $subCategories = SubCategory::where('category_id', $category->id)
                    ->active()
                    ->select('id', 'name', 'image')
                    ->orderBy('id', 'ASC')
                    ->get();

                $subCategoryArr = [];
                if ($subCategories->isNotEmpty()) {
                    foreach ($subCategories as $subCategory) {
                        $subCategoryArr[] = [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'image' => $subCategory->image ? asset('storage/subcategories/' . $subCategory->image) : null,
                        ];
                    }
                }

                $result[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $imageUrl,
                    'sub_categories' => $subCategoryArr ?: null,
                ];
            }

            return $this->toJsonEnc($result, 'Categories fetched successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }

    public function subCategoryList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|integer|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $categoryId = $request->input('category_id');

            $subCategories = SubCategory::where('category_id', $categoryId)
                ->active()
                ->select('id', 'name', 'image')
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($subCategories as $subCategory) {
                if ($subCategory->image) {
                    $subCategory->image = asset('storage/subcategories/' . $subCategory->image);
                }
            }

            if ($subCategories->isEmpty()) {
                return $this->toJsonEnc([], 'No subcategories found', 404);
            }

            return $this->toJsonEnc($subCategories, 'Subcategories fetched successfully', 200);

        } catch (\Exception $e) {
            return $this->toJsonEnc([], $e->getMessage(), 500);
        }
    }
}