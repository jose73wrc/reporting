SELECT 
    revenue_year,
    SUM(invoices) AS revenu,
    SUM(purchase_orders) AS sales_cost,
    SUM(expenses) AS expenses,
    SUM(interest_payments) AS interest,
    SUM(IFNULL(salary_amount, 0)) AS salaries,
    SUM(invoices) - SUM(purchase_orders) AS gross_margin,
    ROUND((SUM(invoices) - SUM(purchase_orders)) * .189,
            2) AS tax,
    ROUND((SUM(invoices) - (SUM(purchase_orders) + SUM(expenses) + SUM(interest_payments) + SUM(IFNULL(salary_amount, 0)))) - (SUM(expenses) + ((SUM(invoices) - SUM(purchase_orders)) * .189)),
            2) AS net,
    ROUND((SUM(invoices) - SUM(purchase_orders)) / SUM(invoices),
            2) AS gross_profit_margin,
    ROUND(((SUM(invoices) - (SUM(purchase_orders) + SUM(expenses) + SUM(interest_payments) + SUM(IFNULL(salary_amount, 0)))) - (SUM(expenses) + ((SUM(invoices) - SUM(purchase_orders)) * .189))) / SUM(invoices),
            2) AS net_profit_margin,
    ROUND((ROUND((SUM(invoices) - (SUM(purchase_orders) + SUM(expenses) + SUM(interest_payments) + SUM(IFNULL(salary_amount, 0)))) - (SUM(expenses) + ((SUM(invoices) - SUM(purchase_orders)) * .189)),
                    2)) / SUM(invoices),
            2) AS roa,
    ROUND(SUM(invoices) - ((SUM(purchase_orders) + SUM(expenses) + ROUND((SUM(invoices) - SUM(purchase_orders)) * .189,
                    2) + SUM(interest_payments))),
            2) AS equity,
    round(ROUND(ROUND((SUM(invoices) - (SUM(purchase_orders) + SUM(expenses) + SUM(interest_payments) + SUM(IFNULL(salary_amount, 0)))) - (SUM(expenses) + ((SUM(invoices) - SUM(purchase_orders)) * .189)),
                    2),
            2) / (ROUND(SUM(invoices) - ((SUM(purchase_orders) + SUM(expenses) + ROUND((SUM(invoices) - SUM(purchase_orders)) * .189,
                    2) + SUM(interest_payments))),
            2)),2) AS roe
FROM
    (SELECT 
        month_id,
            mname,
            revenue_year,
            total_revenue AS invoices,
            vendor_total AS purchase_orders,
            IFNULL(exp_total, 0) AS expenses,
            IFNULL(monthly_interest_payments, 0) AS interest_payments
    FROM
        i3797837_db2.months M
    LEFT JOIN (SELECT 
        MONTH(datef) AS month_num,
            MONTHNAME(datef) AS revenue_month,
            YEAR(datef) AS revenue_year,
            ROUND(SUM(total), 2) AS total_revenue
    FROM
        db2_facture
    GROUP BY YEAR(datef) , MONTH(datef)) I ON (I.month_num = M.month_id)
    LEFT JOIN (SELECT 
        MONTH(datef) AS vendor_month_num,
            MONTHNAME(datef) AS vendor_month,
            YEAR(datef) AS vendor_year,
            ROUND(SUM(total_ttc), 2) AS vendor_total
    FROM
        i3797837_db2.db2_facture_fourn
    GROUP BY YEAR(datef) , MONTH(datef)) V ON (V.vendor_month_num = M.month_id
        AND V.vendor_year = revenue_year)
    LEFT JOIN (SELECT 
        ref,
            MONTH(date_approve) AS month_num,
            DATE(date_approve) AS exp_date,
            MONTHNAME(DATE(date_approve)) AS exp_month,
            YEAR(DATE(date_approve)) AS exp_year,
            ROUND(total_ttc, 2) AS exp_total
    FROM
        db2_expensereport) E ON (E.month_num = M.month_id
        AND E.exp_year = revenue_year)
    LEFT JOIN (SELECT 
        rowid,
            datep AS date_loan_payment,
            MONTH(datep) AS month_num,
            YEAR(datep) AS year_loan_payment,
            MONTHNAME(datep) AS month_loan_payment,
            ROUND(amount_capital, 2) AS interest_payments,
            SUM(ROUND(amount_capital, 2)) AS monthly_interest_payments
    FROM
        db2_loan_schedule
    GROUP BY YEAR(datep) , MONTH(datep)) L ON (L.month_num = M.month_id
        AND L.year_loan_payment = revenue_year)
    ORDER BY revenue_year , month_id) T1
        LEFT JOIN
    (SELECT 
        datep,
            MONTH(datep) AS month_num,
            YEAR(datep) AS salary_payment_year,
            ROUND(amount, 2) AS salary_amount
    FROM
        db2_payment_salary
    GROUP BY YEAR(datep) , MONTH(datep)) B ON (T1.month_id = B.month_num)
WHERE
    revenue_year = 2019
ORDER BY revenue_year , month_id