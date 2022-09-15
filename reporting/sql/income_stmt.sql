SELECT 
    A.month_num AS exp_month,
    revenue_month AS 'month',
    A.revenue_year AS 'year',
    invoices,
    IFNULL(B.total, 0) AS purchase_orders,
    IFNULL(exp_total, 0) AS expense,
    IFNULL(total_loans_monthly, 0) AS loan_payment,
    ifnull(F.amount,0) as salary_payments,
    (invoices - (total + IFNULL(exp_total, 0) + IFNULL(total_loans_monthly, 0))) AS gross_revenue,
    ((.189) * (invoices - (total + IFNULL(exp_total, 0) + IFNULL(total_loans_monthly, 0)))) AS company_tax,
    (invoices - (total + IFNULL(exp_total, 0) + IFNULL(total_loans_monthly, 0))) - ((.189) * (invoices - (total + IFNULL(exp_total, 0) + IFNULL(total_loans_monthly, 0)))) AS net
FROM
    (SELECT 
        MONTH(datef) AS month_num,
            MONTHNAME(datef) AS revenue_month,
            YEAR(datef) AS revenue_year,
            ROUND(SUM(total), 2) AS invoices
    FROM
        db2_facture
    GROUP BY YEAR(datef) , MONTH(datef)) AS A
        LEFT JOIN
    (SELECT 
        MONTH(datef) AS month_num,
            MONTHNAME(datef) AS 'month',
            YEAR(datef) AS 'year',
            ROUND(SUM(total_ttc), 2) AS total
    FROM
        db2_facture_fourn
    GROUP BY YEAR(datef) , MONTH(datef)) B ON (A.month_num = B.month_num
        AND A.revenue_year = B.year)
        LEFT JOIN
    (SELECT 
        ref,
            MONTH(date_approve) AS month_num,
            DATE(date_approve) AS exp_date,
            MONTHNAME(DATE(date_approve)) AS 'month',
            YEAR(DATE(date_approve)) AS 'year',
            ROUND(total_ttc, 2) AS exp_total
    FROM
        db2_expensereport) C ON (A.revenue_month = C.month
        AND A.revenue_year = C.year)
        LEFT JOIN
    (SELECT 
        rowid,
            datep AS date_loan_payment,
            MONTH(datep) AS month_num,
            YEAR(datep) AS year_loan_payment,
            MONTHNAME(datep) AS month_loan_payment,
            ROUND(amount_capital, 2) AS loan_payment_amount,
            SUM(ROUND(amount_capital, 2)) AS total_loans_monthly
    FROM
        db2_loan_schedule
    GROUP BY YEAR(datep) , MONTH(datep)) D ON (D.month_num = A.month_num
        AND D.year_loan_payment = A.revenue_year)
        LEFT JOIN
    (SELECT 
        datep,
            MONTH(datep) AS salary_payment_month,
            YEAR(datep) AS salary_payment_year,
            ROUND(amount, 2) AS amount
    FROM
        db2_payment_salary
    GROUP BY YEAR(datep) , MONTH(datep)) F ON (F.salary_payment_month = A.month_num
        AND F.salary_payment_year = A.revenue_year)
ORDER BY A.revenue_year , A.month_num