<?php

class Fun {
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
     * Format price with currency symbol
     */
    public function formatPrice($price, $currencySymbol = '$') {
        return $currencySymbol . number_format($price, 2);
    }

    /**
     * Generate a random string
     */
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Format date to a readable format
     */
    public function formatDate($date, $format = 'M d, Y') {
        return date($format, strtotime($date));
    }

    /**
     * Calculate nights between two dates
     */
    public function calculateNights($checkIn, $checkOut) {
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $interval = $checkInDate->diff($checkOutDate);
        return $interval->days;
    }

    /**
     * Calculate total price
     */
    public function calculateTotalPrice($pricePerNight, $nights) {
        return $pricePerNight * $nights;
    }

    /**
     * Get years for dropdown
     */
    public function getYears($startYear = null, $endYear = null) {
        $currentYear = date('Y');
        $startYear = $startYear ?? $currentYear - 5;
        $endYear = $endYear ?? $currentYear + 5;
        
        $years = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $years[$year] = $year;
        }
        
        return $years;
    }

    /**
     * Get months for dropdown
     */
    public function getMonths() {
        return [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];
    }

    /**
     * Sanitize input
     */
    public function sanitizeInput($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitizeInput($value);
            }
        } else {
            $input = trim($input);
            $input = stripslashes($input);
            $input = htmlspecialchars($input);
        }
        
        return $input;
    }
}
?> 