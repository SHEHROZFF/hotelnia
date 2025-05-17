<?php

class ProductFun {
    private $db;
    private $security;
    private $dbFunctions;
    private $urlval;
    private $currentDate;

    public function __construct($db, $security, $dbFunctions, $urlval, $currentDate) {
        $this->db = $db;
        $this->security = $security;
        $this->dbFunctions = $dbFunctions;
        $this->urlval = $urlval;
        $this->currentDate = $currentDate;
    }

    /**
     * Get all products
     */
    public function getAllProducts($limit = null) {
        return $this->dbFunctions->getRecords('products', null, null, 'product_id DESC', $limit);
    }

    /**
     * Get product by ID
     */
    public function getProductById($productId) {
        return $this->dbFunctions->getRecord('products', 'product_id', $productId);
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $limit = null) {
        return $this->dbFunctions->getRecords('products', 'category_id', $categoryId, 'product_id DESC', $limit);
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 8) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE is_featured = 1 ORDER BY product_id DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get new products
     */
    public function getNewProducts($days = 30, $limit = 8) {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE created_at >= :date ORDER BY product_id DESC LIMIT :limit");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add product
     */
    public function addProduct($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->dbFunctions->insertRecord('products', $data);
    }

    /**
     * Update product
     */
    public function updateProduct($productId, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->dbFunctions->updateRecord('products', $data, 'product_id', $productId);
    }

    /**
     * Delete product
     */
    public function deleteProduct($productId) {
        return $this->dbFunctions->deleteRecord('products', 'product_id', $productId);
    }

    /**
     * Search products
     */
    public function searchProducts($keyword) {
        $pdo = $this->db->getConnection();
        $searchTerm = "%$keyword%";
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :keyword OR product_description LIKE :keyword ORDER BY product_id DESC");
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get product images
     */
    public function getProductImages($productId) {
        return $this->dbFunctions->getRecords('product_images', 'product_id', $productId, 'sort_order ASC');
    }

    /**
     * Check if product is available
     */
    public function isProductAvailable($productId, $checkIn, $checkOut) {
        // This would need to be customized based on your booking system
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as booking_count 
            FROM bookings 
            WHERE product_id = :product_id 
            AND (
                (check_in_date <= :check_in AND check_out_date >= :check_in) OR
                (check_in_date <= :check_out AND check_out_date >= :check_out) OR
                (check_in_date >= :check_in AND check_out_date <= :check_out)
            )
        ");
        
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':check_in', $checkIn);
        $stmt->bindParam(':check_out', $checkOut);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['booking_count'] == 0;
    }
}
?> 