<?php

namespace App\Formatter;

use App\Entity\Category;

class CategoryFormatter
{
    /**
     * @param Category $category
     *
     * @return array
     */
    public function format(Category $category): array
    {
        return [
            'category_id'    => $category->getId(),
            'category_code'  => $category->getCode(),
            'category_name'  => $category->getNameDefault(),
            'category_color' => $category->getColor(),
            'category_icon'  => $category->getIcon(),
            'category_level' => $category->getLevel(),
            'category_rank'  => $category->getRank(),
            'category_unit'  => $category->getUnit(),
        ];
    }
}
