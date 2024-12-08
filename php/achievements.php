<?php
// achievements.php
function caluculateWeeklyAchievements($pdo, $user_id, $week_start_date)
{
    // 週の終了日（月曜）を計算する
    $week_end_date = date("Y-m-d", strtotime($week_start_date . "+6 days"));

    //1週間分のデータを取得
    $sql = "SELECT * FROM daily_records
    WHERE user_id = :user_id AND record_date BETWEEN :start_date AND :end_date
    ORDER BY record_date";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
$stmt->bindValue(":start_date", $week_start_date);
$stmt->bindValue(":end_date", $week_end_date);
$stmt->execute();
$weekly_date = $stmt->fetchAll(PDO::FETCH_ASSOC);

//称号の判定
$early_riser_count = 0; //6時前起床カウント
$total_meal_score = 0; //食事スコア
$total_exercise_score = 0; //運動スコア
$total_study_hours = 0; //学習時間合計
$days_recorded = count($weekly_date); //記録がある日を数える

foreach($weekly_date as $day){
    //早起き判定
    $week_time = new DateTime($day["sleep_end"]);
    if($week_time->format("H")<6){
        $early_riser_count ++;
    }
    //書くスコアの合計
    $total_meal_score += $day["meal_score"];
    $total_exercise_score += $day["exercise_score"];
    $total_study_hours += $day["study_hours"];
}
//称号の判定（記録がある日のみで計算する場合）
$achievements = [
    "early_riser_pro" => ($early_riser_count >= 4),
    "nutrition_mister" => ($days_recorded > 0 && ($total_meal_score / ($days_recorded *10)) >= 0.8),
    "active_natural" => ($days_recorded > 0 && ($total_exercise_score / ($days_recorded*5)) >=0.8),
    "self_study_rocket" => ($days_recorded > 0 && ($total_study_hours / $days_recorded) >= 6)
];

//DBへ保存
$sql = "INSERT INTO weekly_achievements
        (user_id, week_start_date, week_end_date, early_riser_pro, nutrition_minister, active_natural, self_study_rocket)
        VALUES
        (:user_id, :week_start_date, :week_end_date, :early_riser_pro, :nutrition_minister, :active_natural, :self_study_rocket)
        ON DUPLICATE KEY UPDATE
        early_riser_pro = :early_riser_pro,
        nutrition_minister = :nutrition_minister,
        active_natural = :active_natural,
        self_study_rocket = :self_study_rocket";

$stmt = $pdo->prepare($sql);
$stmt->bindValue("user_id", $user_id);
$stmt->bindValue("week_start_date", $week_start_date);
$stmt->bindValue(':week_end_date', $week_end_date);
$stmt->bindValue(':early_riser_pro', $achievements['early_riser_pro'], PDO::PARAM_BOOL);
$stmt->bindValue(':nutrition_minister', $achievements['nutrition_minister'], PDO::PARAM_BOOL);
$stmt->bindValue(':active_natural', $achievements['active_natural'], PDO::PARAM_BOOL);
$stmt->bindValue(':self_study_rocket', $achievements['self_study_rocket'], PDO::PARAM_BOOL);

return $stmt->execute();
}
