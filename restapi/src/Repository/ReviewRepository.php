<?php declare(strict_types=1);

namespace App\Repository;

use App\Exception\ReviewException;
use PDO;

class ReviewRepository extends BaseRepository
{
    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetReview(int $review_id, int $user_id)
    {
        $query = 'SELECT * FROM reviews WHERE id = :review_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $review_id);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $review = $statement->fetchObject();
        if (empty($review)) {
            throw new ReviewException('Task not found.', 404);
        }

        return $review;
    }

    public function getAllReviews(): array
    {
        $query = 'SELECT * FROM reviews ORDER BY id';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getReviews(int $user_id): array
    {
        $query = 'SELECT * FROM reviews WHERE user_id = :user_id ORDER BY id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();

        return $statement->fetchAll();
    }

    # TODO: Discuss about searching reviews

//    public function searchReviews($tasksName, int $userId, $status): array
//    {
//        $query = $this->getSearchTasksQuery($status);
//        $name = '%' . $tasksName . '%';
//        $statement = $this->getDb()->prepare($query);
//        $statement->bindParam('name', $name);
//        $statement->bindParam('userId', $userId);
//        if ($status === 0 || $status === 1) {
//            $statement->bindParam('status', $status);
//        }
//        $statement->execute();
//
//        return $statement->fetchAll();
//    }
//
//    private function getSearchTasksQuery($status)
//    {
//        $statusQuery = '';
//        if ($status === 0 || $status === 1) {
//            $statusQuery = 'AND status = :status';
//        }
//        $query = "
//            SELECT * FROM tasks
//            WHERE name LIKE :name AND userId = :userId $statusQuery
//            ORDER BY id
//        ";
//
//        return $query;
//    }

    public function createReview($review)
    {
        $query = '
            INSERT INTO reviews (user_id, `q&a`, geo_tag, device_signature)
            VALUES (:user_id, :q_a, :geo_tag, :device_signature)
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $review->user_id);
        $statement->bindParam('q_a', $review->q_a);
        $statement->bindParam('geo_tag', $review->geo_tag);
        $statement->bindParam('device_signature', $review->device_signature);
        $statement->execute();

        return $this->checkAndGetReview((int) $this->database->lastInsertId(), (int) $review->user_id);
    }

    # TODO: Discuss about update reviews

    public function updateReview($review)
    {
        $query = '
            UPDATE reviews
            SET `q&a`=:q_a
            WHERE id=:id AND user_id = :user_id
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $review->id);
        $statement->bindParam('q_a', $review->q_a);
        $statement->bindParam('user_id', $review->user_id);
        $statement->execute();

        return $this->checkAndGetReview((int) $review->id, (int) $review->user_id);
    }

    public function deleteReview(int $review_id, int $user_id): string
    {
        $query = 'DELETE FROM reviews WHERE id = :id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $review_id);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();

        return 'The task was deleted.';
    }
}
