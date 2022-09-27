<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2022 Rance Aaron <ranceaaron941@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   reporting     Module Reporting
 *  \brief      Reporting module descriptor.
 *
 *  \file       htdocs/reporting/core/modules/modReporting.class.php
 *  \ingroup    reporting
 *  \brief      Description and activation file for module Reporting
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module Reporting
 */
class CashFlow extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

        $data = array();
                
    }

    public function get_cashflow($databasetable){       
        $data = array();
        if(!isset($_REQUEST['select_year'])){
            $stmt_year = 2020;
        }else{
            $stmt_year = $_REQUEST['select_year'];
        }
        $sql ="SELECT
        revenue_year,
        sum(interest_payments) as debt_payments,
        SUM(invoices)-(sum(purchase_orders)+sum(expenses)+sum(salaries)) as net_income,
        SUM(invoices)-(sum(purchase_orders)+sum(expenses)+sum(salaries))-sum(interest_payments) as ending_cash_balance
    FROM
        (SELECT 
            month_id,
                mname,
                revenue_year,
                invoices,
                purchase_orders,
                expenses,
                interest_payments,
                IFNULL(salary_amount, 0) AS salaries,
                invoices - (purchase_orders + expenses + interest_payments + IFNULL(salary_amount, 0)) AS gross,
                (invoices - (purchase_orders + expenses + interest_payments + IFNULL(salary_amount, 0))) * .189 AS tax,
                (invoices - (purchase_orders + expenses + interest_payments + IFNULL(salary_amount, 0))) - ((invoices - (purchase_orders + expenses + interest_payments + IFNULL(salary_amount, 0))) * .189) AS net
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
            months M
        LEFT JOIN (SELECT 
            MONTH(datef) AS month_num,
                MONTHNAME(datef) AS revenue_month,
                YEAR(datef) AS revenue_year,
                ROUND(SUM(total_ttc), 2) AS total_revenue
        FROM
            ".$databasetable."_facture
        GROUP BY YEAR(datef) , MONTH(datef)) I ON (I.month_num = M.month_id)
        LEFT JOIN (SELECT 
            MONTH(datef) AS vendor_month_num,
                MONTHNAME(datef) AS vendor_month,
                YEAR(datef) AS vendor_year,
                ROUND(SUM(total_ttc), 2) AS vendor_total
        FROM
            ".$databasetable."_facture_fourn
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
            ".$databasetable."_expensereport) E ON (E.month_num = M.month_id
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
            ".$databasetable."_loan_schedule
        GROUP BY YEAR(datep) , MONTH(datep)) L ON (L.month_num = M.month_id
            AND L.year_loan_payment = revenue_year)
        ORDER BY revenue_year , month_id) T1
        LEFT JOIN (SELECT 
            datep,
                MONTH(datep) AS month_num,
                YEAR(datep) AS salary_payment_year,
                ROUND(amount, 2) AS salary_amount
        FROM
            ".$databasetable."_payment_salary
        GROUP BY YEAR(datep) , MONTH(datep)) B ON (T1.month_id = B.month_num)
        ORDER BY revenue_year , month_id) T2
        where revenue_year = ".$stmt_year."       
    group by revenue_year";
        		
        $data = array();
        $result=$this->db->query($sql);			
        
        while ($row = $this->db->fetch_array($result)) {
            $data[][] = $row;
        }

        $this->db->free($result);         
        $k = array();
        foreach($data as $d=>$f){
           foreach($f as $v){
               if(!is_integer($f)){
                $k[] = $v;
               }
           }
        }

        $t = '<h4 style="text-align:center">Statement of Cash Flows</h4>';
        $t .= '<table class="noborder"><thead><th>Year</th><th>Debt Payments</th><th>Net Income</th><th>Cash Balance</th></thead>';
        foreach($k as $j){
            $t .= '<tr>';
            foreach($j as $x=>$y){
                if(!is_integer($x)){
                    $t .= '<td>'.$y.'</td>';
                }                
            }
            $t .= '</tr>';
        }
        $t .= '</table>';

        return $t;
      
    }

    
}