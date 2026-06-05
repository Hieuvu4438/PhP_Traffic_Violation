<?php
namespace App\Models;

use App\Core\Model;

class News extends Model
{
    protected string $table = 'news';

    public function getPublished(array $conditions = [], int $limit = 0, int $offset = 0): array
    {
        $conditions['status'] = 'published';
        return $this->all($conditions, 'created_at DESC', $limit, $offset);
    }

    public function getWithCategory(int $id): ?array
    {
        $sql = "SELECT n.*, nc.name as category_name, u.fullname as author_name
                FROM news n
                LEFT JOIN news_categories nc ON n.category_id = nc.id
                LEFT JOIN users u ON n.author_id = u.id
                WHERE n.id = :id AND n.status = 'published'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAllWithCategory(array $conditions = [], string $orderBy = 'n.created_at DESC', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT n.*, nc.name as category_name
                FROM news n
                LEFT JOIN news_categories nc ON n.category_id = nc.id";
        $params = [];

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $key => $value) {
                $prefix = str_contains($key, '.') ? '' : 'n.';
                $clauses[] = "{$prefix}{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= " ORDER BY {$orderBy}";

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE news SET views = views + 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getLatest(int $limit = 6): array
    {
        return $this->getPublished([], $limit, 0);
    }

    public function getRelated(int $categoryId, int $excludeId, int $limit = 4): array
    {
        $sql = "SELECT * FROM news
                WHERE category_id = :cat AND id != :exc AND status = 'published'
                ORDER BY created_at DESC LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('cat', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue('exc', $excludeId, \PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
