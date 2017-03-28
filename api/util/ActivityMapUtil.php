<?php
/**
 * User: David
 * Date: 2017-03-12
 * Time: 5:29 PM
 */
namespace Evn\util;

/**
 * Class ActivityMapUtil
 * @package Evn\util
 * Will provide an array of Activities that are mapped to
 * the passed Detail ID
 */
class ActivityMapUtil {

    /**
     * Maps the activities associated with the given detail (id)
     * @param $db
     * @param $detailId
     * @return array
     */
    public static function mapToDetail($db, $detailId, $categories, $activities) {
        $query = 'SELECT `m1`.`activity_id` as id,`a`.`name` as activity_name,`c`.`name` as category_name '
            . 'FROM detail_activity_map as `m1` '
            . 'LEFT JOIN activity as `a` ON `m1`.`activity_id`=`a`.`id` '
            . 'LEFT JOIN category_activity_map as `m2` ON `m2`.`activity_id`=`m1`.`activity_id` '
            . 'LEFT JOIN category as `c` ON `c`.`id`=`m2`.`category_id` '
            . 'WHERE `m1`.`detail_id`=:detailId';

        if (!empty($categories)) {
            $query .= " AND (";
            $category_query = array();
            foreach($categories as $key => $value) {
                $category_query[] = '`m2`.`category_id`=:category'.$key;
            }
            $query .= implode(' OR ', $category_query) . ")";

            if (!empty($activities)) {
                $query .= " AND (";
                $activity_query = array();
                foreach($activities as $key => $value) {
                    $activity_query[] = '`m1`.`activity_id`=:activity'.$key;
                }
                $query .= implode(' OR ', $activity_query) . ")";
            }
        }
        $stmt = $db->prepare($query);

        /*** bind the paramaters ***/
        $stmt->bindParam(':detailId', $detailId, \PDO::PARAM_INT);

        /*** bind the categories ***/
        if (!empty($categories)) {
            foreach ($categories as $key => &$value) {
                $stmt->bindParam(':category' . $key, $value);
            }
            if (!empty($activities)) {
                foreach ($activities as $key => &$value) {
                    $stmt->bindParam(':activity' . $key, $value);
                }
            }
        }

        $stmt->execute();

        $activities = array();
        while (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $activity = new \Evn\model\Activity();
            $activity->id = $row['id'];
            $activity->name = $row['activity_name'];
            $activity->category = $row['category_name'];

            $activities[] = $activity;
        }
        return $activities;
        //return $query;
    }

    /**
     * Maps the activities associated with the given category (id)
     * @param $db
     * @param $detailId
     * @return array
     */
    public static function mapToCategory($db, $categoryId) {
        $query = 'SELECT `a`.`id` as id,`a`.`name` as activity_name,`c`.`name` as category_name '
            . 'FROM activity as `a` '
            . 'LEFT JOIN category_activity_map as `m` ON `m`.`activity_id`=`a`.`id` '
            . 'LEFT JOIN category as `c` ON `c`.`id`=`m`.`category_id` '
            . 'WHERE `m`.`category_id`=:categoryId';

        $stmt = $db->prepare($query);

        /*** bind the paramaters ***/
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);

        $stmt->execute();

        $activities = array();
        while (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $activity = new \Evn\model\Activity();
            $activity->id = $row['id'];
            $activity->name = $row['activity_name'];
            $activity->category = $row['category_name'];

            $activities[] = $activity;
        }
        return $activities;
    }
}