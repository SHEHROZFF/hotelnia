<?php

class CategoryManager {
    private $db;
    private $security;
    private $dbFunctions;
    private $urlval;

    public function __construct($db, $security, $dbFunctions, $urlval) {
        $this->db = $db;
        $this->security = $security;
        $this->dbFunctions = $dbFunctions;
        $this->urlval = $urlval;
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        return $this->dbFunctions->getRecords('categories', null, null, 'category_name ASC');
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($categoryId) {
        return $this->dbFunctions->getRecord('categories', 'category_id', $categoryId);
    }

    /**
     * Add new category
     */
    public function addCategory($categoryName, $categoryDescription = '', $parentId = 0, $isActive = 1) {
        $data = [
            'category_name' => $categoryName,
            'category_description' => $categoryDescription,
            'parent_id' => $parentId,
            'is_active' => $isActive,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->dbFunctions->insertRecord('categories', $data);
    }

    /**
     * Update category
     */
    public function updateCategory($categoryId, $categoryName, $categoryDescription = '', $parentId = 0, $isActive = 1) {
        $data = [
            'category_name' => $categoryName,
            'category_description' => $categoryDescription,
            'parent_id' => $parentId,
            'is_active' => $isActive,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->dbFunctions->updateRecord('categories', $data, 'category_id', $categoryId);
    }

    /**
     * Delete category
     */
    public function deleteCategory($categoryId) {
        return $this->dbFunctions->deleteRecord('categories', 'category_id', $categoryId);
    }

    /**
     * Get parent categories
     */
    public function getParentCategories() {
        return $this->dbFunctions->getRecords('categories', 'parent_id', 0);
    }

    /**
     * Get child categories
     */
    public function getChildCategories($parentId) {
        return $this->dbFunctions->getRecords('categories', 'parent_id', $parentId);
    }

    /**
     * Check if category has children
     */
    public function hasChildren($categoryId) {
        $children = $this->dbFunctions->getRecords('categories', 'parent_id', $categoryId);
        return count($children) > 0;
    }

    /**
     * Get category tree
     */
    public function getCategoryTree() {
        $categories = $this->getAllCategories();
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == 0) {
                $tree[$category['category_id']] = [
                    'name' => $category['category_name'],
                    'children' => []
                ];
            }
        }

        foreach ($categories as $category) {
            if ($category['parent_id'] != 0 && isset($tree[$category['parent_id']])) {
                $tree[$category['parent_id']]['children'][$category['category_id']] = [
                    'name' => $category['category_name'],
                    'children' => []
                ];
            }
        }

        return $tree;
    }
}
?> 