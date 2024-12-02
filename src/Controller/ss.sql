SELECT SUM(`transaction`.`amount`) As total, 
DATE(`transaction`.`created_at`) As creationDate FROM
 `transaction` GROUP By DATE(`transaction`.`created_at`);

 SELECT SUM(`transaction`.`amount`) As total, DATE(`transaction`.`created_at`) As creationDate 
 FROM `transaction` INNER JOIN `route` r ON `transaction`.`route_id` = r.id INNER JOIN `vehicle` 
 v ON r.vehicle_id = v.id WHERE v.id = 2 GROUP By DATE(`transaction`.`created_at`);


 SELECT SUM(tr.`amount`) As total, DATE(tr.`created_at`) As creationDate,ve.totalVers,
  ve.versDate FROM `transaction` tr LEFT JOIN( SELECT SUM(`versement`.`amount`) AS totalVers,
   DATE(`versement`.`created_at`) As versDate FROM `versement` GROUP By DATE(`versement`.`created_at`) )
  ve ON Date(tr.`created_at`) = ve.versDate GROUP By DATE(tr.`created_at`);


 SELECT SUM(IFNULL(tr.`amount`,0)) As total, DATE(tr.`created_at`) As creationDate,
 IFNULL(ve.totalVers,0)As totalVers, IFNULL(ve.versDate, DATE(tr.`created_at`)) AS versDate,
  ( SUM(IFNULL(tr.`amount`,0)) + IFNULL(ve.totalVers,0)) AS somme FROM `transaction` tr 
  LEFT JOIN( SELECT SUM(`versement`.`amount`) AS totalVers, DATE(`versement`.`created_at`) As
   versDate FROM `versement` GROUP By DATE(`versement`.`created_at`) ) ve ON Date(tr.`created_at`)
    = ve.versDate GROUP By DATE(tr.`created_at`)
UNION
SELECT SUM(IFNULL(tr.`amount`,0)) As total, DATE( IFNULL(tr.`created_at`,ve.versDate)) As creationDate,IFNULL(ve.totalVers,0) As totalVers, IFNULL(ve.versDate,DATE(tr.`created_at`)) AS versDate, (SUM(IFNULL(tr.`amount`,0)) + IFNULL(ve.totalVers,0)) AS somme FROM `transaction` tr RIGHT JOIN( SELECT SUM(`versement`.`amount`) AS totalVers, 
DATE(`versement`.`created_at`) As versDate FROM `versement` GROUP By DATE(`versement`.`created_at`) ) ve ON Date(tr.`created_at`) = ve.versDate GROUP By DATE(tr.`created_at`)
;

SELECT *, (v.total + IFNULL(x.totalVers,0)) As somme FROM (SELECT SUM(IFNULL(tr.`amount`,0)) As total, DATE(tr.`created_at`) As creationDate FROM `transaction` tr INNER JOIN `route` r ON tr.route_id = r.id WHERE r.vehicle_id = 3  GROUP By DATE(tr.`created_at`) ) v   LEFT JOIN
(SELECT SUM(`versement`.`amount`) AS totalVers, DATE(`versement`.`created_at`) As
   versDate FROM `versement` WHERE `versement`.`vehicle_id` = 3 GROUP By DATE(`versement`.`created_at`) ) x ON v.creationDate = x.versDate
UNION
SELECT *, (IFNULL(v.total,0) + x.totalVers) As somme FROM (SELECT SUM(IFNULL(tr.`amount`,0)) As total, DATE(tr.`created_at`) As creationDate FROM `transaction` tr INNER JOIN `route` r ON tr.route_id = r.id WHERE r.vehicle_id = 3 GROUP By DATE(tr.`created_at`) ) v   RIGHT JOIN
(SELECT SUM(`versement`.`amount`) AS totalVers, DATE(`versement`.`created_at`) As
   versDate FROM `versement` WHERE `versement`.`vehicle_id` = 3 GROUP By DATE(`versement`.`created_at`) ) x ON v.creationDate = x.versDate