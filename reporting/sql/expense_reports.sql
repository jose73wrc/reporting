SELECT 
    ref,
    month(date_approve) as month_num,
    DATE(date_approve) AS exp_date,
    MONTHNAME(DATE(date_approve)) AS exp_month,
    YEAR(DATE(date_approve)) AS exp_year,
    ROUND(total_ttc, 2) AS exp_total
FROM
    db2_expensereport
