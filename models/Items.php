<?php
class Items
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::connect();
    }
    
    /**
     * 取得所有道具
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM items ORDER BY price");
        return $stmt->fetchAll();
    }
    
    /**
     * 根據id取得道具
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 根據code取得道具
     */
    public function getByCode($code)
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
}