<?php

class Hotel {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all hotels
     */
    public function getAllHotels($limit = null, $page = 1, $featured = false) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT h.*, 
                (SELECT MIN(price_per_night) FROM rooms WHERE hotel_id = h.hotel_id) AS min_price,
                (SELECT MAX(price_per_night) FROM rooms WHERE hotel_id = h.hotel_id) AS max_price,
                (SELECT image_path FROM hotel_images WHERE hotel_id = h.hotel_id AND is_primary = 1 LIMIT 1) AS primary_image,
                (SELECT COUNT(*) FROM hotel_amenities WHERE hotel_id = h.hotel_id) AS amenities_count
                FROM hotels h WHERE h.is_active = 1";
        
        if ($featured) {
            $sql .= " AND h.is_featured = 1";
        }
        
        $sql .= " ORDER BY h.is_featured DESC, h.hotel_id DESC";
        
        if ($limit !== null) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $pdo->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count total hotels (for pagination)
     */
    public function countHotels($featured = false) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) FROM hotels WHERE is_active = 1";
        
        if ($featured) {
            $sql .= " AND is_featured = 1";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get a single hotel by ID
     */
    public function getHotelById($hotelId) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT h.*, 
                (SELECT MIN(price_per_night) FROM rooms WHERE hotel_id = h.hotel_id) AS min_price,
                (SELECT MAX(price_per_night) FROM rooms WHERE hotel_id = h.hotel_id) AS max_price
                FROM hotels h 
                WHERE h.hotel_id = :hotelId AND h.is_active = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get hotel images
     */
    public function getHotelImages($hotelId) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT * FROM hotel_images 
                WHERE hotel_id = :hotelId 
                ORDER BY is_primary DESC, sort_order ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get hotel rooms
     */
    public function getHotelRooms($hotelId) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT r.*,
                (SELECT image_path FROM room_images WHERE room_id = r.room_id AND is_primary = 1 LIMIT 1) AS primary_image
                FROM rooms r
                WHERE r.hotel_id = :hotelId AND r.is_available = 1
                ORDER BY r.price_per_night ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get room images
     */
    public function getRoomImages($roomId) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT * FROM room_images 
                WHERE room_id = :roomId 
                ORDER BY is_primary DESC, sort_order ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get hotel amenities
     */
    public function getHotelAmenities($hotelId) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT a.* FROM amenities a
                JOIN hotel_amenities ha ON a.amenity_id = ha.amenity_id
                WHERE ha.hotel_id = :hotelId
                ORDER BY a.amenity_name ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Search hotels by criteria
     */
    public function searchHotels($criteria, $limit = null, $page = 1) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT h.*, 
                (SELECT MIN(price_per_night) FROM rooms WHERE hotel_id = h.hotel_id) AS min_price,
                (SELECT MAX(price_per_night) FROM rooms WHERE hotel_id = h.hotel_id) AS max_price,
                (SELECT image_path FROM hotel_images WHERE hotel_id = h.hotel_id AND is_primary = 1 LIMIT 1) AS primary_image,
                (SELECT COUNT(*) FROM hotel_amenities WHERE hotel_id = h.hotel_id) AS amenities_count
                FROM hotels h 
                LEFT JOIN rooms r ON h.hotel_id = r.hotel_id
                WHERE h.is_active = 1";
        
        $params = [];
        
        // Search by destination (city or country)
        if (!empty($criteria['destination'])) {
            $sql .= " AND (h.city LIKE :destination OR h.country LIKE :destination)";
            $params['destination'] = '%' . $criteria['destination'] . '%';
        }
        
        // Filter by hotel type
        if (!empty($criteria['hotel_type'])) {
            $sql .= " AND h.hotel_type = :hotel_type";
            $params['hotel_type'] = $criteria['hotel_type'];
        }
        
        // Filter by price range
        if (!empty($criteria['min_price']) && !empty($criteria['max_price'])) {
            $sql .= " AND r.price_per_night BETWEEN :min_price AND :max_price";
            $params['min_price'] = $criteria['min_price'];
            $params['max_price'] = $criteria['max_price'];
        } else if (!empty($criteria['min_price'])) {
            $sql .= " AND r.price_per_night >= :min_price";
            $params['min_price'] = $criteria['min_price'];
        } else if (!empty($criteria['max_price'])) {
            $sql .= " AND r.price_per_night <= :max_price";
            $params['max_price'] = $criteria['max_price'];
        }
        
        // Filter by amenities
        if (!empty($criteria['amenities']) && is_array($criteria['amenities'])) {
            // Create named parameters for amenities
            $amenityParams = [];
            foreach ($criteria['amenities'] as $index => $amenityId) {
                $paramName = ":amenity_" . $index;
                $amenityParams[] = $paramName;
                $params['amenity_' . $index] = $amenityId;
            }
            
            // Modified: Show hotels that have at least one of the selected amenities
            // This is more user-friendly and ensures results are returned
            $sql .= " AND h.hotel_id IN (
                    SELECT hotel_id FROM hotel_amenities 
                    WHERE amenity_id IN (" . implode(',', $amenityParams) . ")
                    GROUP BY hotel_id)";
        }
        
        // Filter by check-in date
        if (!empty($criteria['check_in_date'])) {
            // This is a simplified approach - in a real system you would check for availability
            $sql .= " AND h.is_active = 1"; // Placeholder for actual date filtering
        }
        
        // Handle sorting
        if (!empty($criteria['sort'])) {
            switch ($criteria['sort']) {
                case 'price_asc':
                    $sql .= " ORDER BY min_price ASC";
                    break;
                case 'price_desc':
                    $sql .= " ORDER BY min_price DESC";
                    break;
                case 'rating':
                    $sql .= " ORDER BY h.star_rating DESC";
                    break;
                case 'featured':
                default:
                    $sql .= " ORDER BY h.is_featured DESC, h.hotel_id DESC";
                    break;
            }
        } else {
            $sql .= " ORDER BY h.is_featured DESC, h.hotel_id DESC";
        }
        
        if ($limit !== null) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $pdo->prepare($sql);
        
        // Bind named parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count search results (for pagination)
     */
    public function countSearchResults($criteria) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT COUNT(DISTINCT h.hotel_id) FROM hotels h
                LEFT JOIN rooms r ON h.hotel_id = r.hotel_id
                WHERE h.is_active = 1";
        
        $params = [];
        
        // Search by destination (city or country)
        if (!empty($criteria['destination'])) {
            $sql .= " AND (h.city LIKE :destination OR h.country LIKE :destination)";
            $params['destination'] = '%' . $criteria['destination'] . '%';
        }
        
        // Filter by hotel type
        if (!empty($criteria['hotel_type'])) {
            $sql .= " AND h.hotel_type = :hotel_type";
            $params['hotel_type'] = $criteria['hotel_type'];
        }
        
        // Filter by price range
        if (!empty($criteria['min_price']) && !empty($criteria['max_price'])) {
            $sql .= " AND r.price_per_night BETWEEN :min_price AND :max_price";
            $params['min_price'] = $criteria['min_price'];
            $params['max_price'] = $criteria['max_price'];
        } else if (!empty($criteria['min_price'])) {
            $sql .= " AND r.price_per_night >= :min_price";
            $params['min_price'] = $criteria['min_price'];
        } else if (!empty($criteria['max_price'])) {
            $sql .= " AND r.price_per_night <= :max_price";
            $params['max_price'] = $criteria['max_price'];
        }
        
        // Filter by amenities - updated to match searchHotels method
        if (!empty($criteria['amenities']) && is_array($criteria['amenities'])) {
            // Create named parameters for amenities
            $amenityParams = [];
            foreach ($criteria['amenities'] as $index => $amenityId) {
                $paramName = ":amenity_" . $index;
                $amenityParams[] = $paramName;
                $params['amenity_' . $index] = $amenityId;
            }
            
            $sql .= " AND h.hotel_id IN (
                    SELECT hotel_id FROM hotel_amenities 
                    WHERE amenity_id IN (" . implode(',', $amenityParams) . ")
                    GROUP BY hotel_id)";
        }
        
        $stmt = $pdo->prepare($sql);
        
        // Bind named parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Get all amenities for filter dropdown
     */
    public function getAllAmenities() {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT * FROM amenities ORDER BY amenity_name ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all hotel types for filter dropdown
     */
    public function getAllHotelTypes() {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT hotel_type FROM hotels WHERE is_active = 1 ORDER BY hotel_type ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Get top destinations (cities with most hotels)
     */
    public function getTopDestinations($limit = 6) {
        $pdo = $this->db->getConnection();
        
        $sql = "SELECT city, country, COUNT(*) AS hotel_count 
                FROM hotels 
                WHERE is_active = 1 
                GROUP BY city, country 
                ORDER BY hotel_count DESC, city ASC 
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get featured hotels for homepage
     */
    public function getFeaturedHotels($limit = 6) {
        return $this->getAllHotels($limit, 1, true);
    }
}
?> 