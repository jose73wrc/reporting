SELECT 
    rowid,
    datep as date_loan_payment,
    month(datep) as month_num,
    year(datep) as year_loan_payment,
    monthname(datep) as month_loan_payment,
    round(amount_capital,2) as interest_payments,
    sum(round(amount_capital,2)) as monthly_interest_payments
FROM
    db2_loan_schedule
group by year(datep), month(datep);