SELECT 
    month_id,
    mname,
    revenue_year,
    invoices,
    purchase_orders,
    expenses,
    interest_payments,
    ifnull(salary_amount,0) as salaries,
    invoices-purchase_orders as gross_margin,
    invoices-(purchase_orders+expenses+interest_payments+ifnull(salary_amount,0)) as operating_income,
    (invoices-purchase_orders)*.189 as tax,
    (invoices-(purchase_orders+expenses+interest_payments+ifnull(salary_amount,0)))-(expenses+((invoices-purchase_orders)*.189)) as net
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
    order by revenue_year, month_id