SELECT 
    datep,
    MONTH(datep) AS month_num,
    YEAR(datep) AS salary_payment_year,
    ROUND(amount, 2) AS salary_amount
FROM
    db2_payment_salary
GROUP BY YEAR(datep) , MONTH(datep);