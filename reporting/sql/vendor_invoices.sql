SELECT 
    monthname(datef) as 'month',
    year(datef) as 'year',
    round(sum(total_ttc),2) as total
FROM
    i3797837_db2.db2_facture_fourn
    group by year(datef), month(datef);