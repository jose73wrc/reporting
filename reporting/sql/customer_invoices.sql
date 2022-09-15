SELECT 
    month(datef) as month_num,
	monthname(datef) as revenue_month,
    year(datef) as revenue_year,
    round(sum(total),2) as total_revenue
    
FROM
    i3797837_db2.db2_facture
group by year(datef), month(datef);