<?php
/**
 * Created on Apr 11, 2012
 *
 * Parses a different XML files.
 * 
 */
require_once 'rest_connector2.php';
require_once 'displayImages.php';
require_once 'session.php';

function parseMe($xmlfile, $obj) {
	$array = simplexml_load_string($xmlfile);
	//$resource = key($array);
	//print_r($array);
	
	switch ($obj) {
		case "invoices": {
			$x = 0;
			echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\">" .
					"<td>id</td><td>invoice</td><td>customer</td><td>total</td><td>date created&nbsp;&nbsp;</td></tr>";
			while ($array->invoice[$x]) {
				echo "<tr>";
				echo "<td>" . $array->invoice[$x]->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td><code>" . $array->invoice[$x]->invoice_id ."</code>&nbsp;&nbsp;</td>";
				echo "<td>" . $array->invoice[$x]->invoice_customer->mainname . "&nbsp;&nbsp;</td>";
				echo "<td>\$" . money_format('%i', (string)$array->invoice[$x]->totals->total) . "&nbsp;&nbsp;</td>";
				echo "<td>" . substr($array->invoice[$x++]->datetime_created,0,10) ."&nbsp;&nbsp;</td>";
				echo "</tr>";
			}
			echo "</table>";
			break;
		}
		
		case "invoice": {
			$x = 0;
			echo "<table border=\"0\">";
			if ($array) {
				echo "<tr>";
				echo "<td>" . $array->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td><code>" . $array->invoice_id ."</code>&nbsp;&nbsp;</td>";
				echo "<td>" . $array->invoice_customer->mainname . "&nbsp;&nbsp;</td>";
				echo "<td>\$" . money_format('%i', (string)$array->totals->total) . "</td>";
				echo "</tr>";
			}
			echo "</table>";
			break;
		}
		
		case "lineitem": {
			$x = 0;
			echo "<table border=\"0\">";
			if ($array) {
				echo "<tr>";
				echo "<td>" . $array->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td><code>" . $array->lineitem_product->description ."</code>&nbsp;&nbsp;</td>";
				echo "<td>sell= \$" . money_format('%i', (string)$array->sells->sell) . "&nbsp;&nbsp;</td>";
				echo "<td>base= \$" . money_format('%i', (string)$array->sells->base) . "&nbsp;&nbsp;</td>";
				echo "<td>" . $array->quantity . "&nbsp;&nbsp;</td>";
				echo "<td>discount= " . $array->discount ."</td>";
				echo "<td><code>" . $array->discount->attributes()->type ."</code>&nbsp;&nbsp;</td>";
				echo "<td>total= \$" . money_format('%i', (string)$array->sells->total) . "&nbsp;&nbsp;</td>";
				echo "</tr>";
			}
			echo "</table><br>";
			break;
		}
		
		case "lineitems": {
			$x = 0;
			echo "<table border=\"0\"><tr>";
			while ($array->lineitem[$x]) {
				echo "<tr>";
				echo "<td>" . $array->lineitem[$x++]->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "</tr>";
			}
			echo "</tr></table>";
			break;
		}
						
		case "products": {
			$x = 0;
			echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\">" .
					"<td>id</td><td>product code</td><td>description</td>" .
					"<td>price</td><td>in stock</td><td>details</td></tr>";
			while ($array->product[$x]) {
				echo "<tr>";
				echo "<td>" . $array->product[$x]->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td><code>" . $array->product[$x]->code ."</code>&nbsp;&nbsp;</td>";
				echo "<td>" . $array->product[$x]->description . "&nbsp;&nbsp;</td>";
				echo "<td>\$" . money_format('%i', (string)$array->product[$x]->sell_price) . "&nbsp;&nbsp;</td>";
				echo "<td><b>" . round($array->product[$x]->inventory->available) . "</b>&nbsp;&nbsp;</td>";
                echo "<td><a href=/viewDetails.php?resource=product&uri=".$array->product[$x++]->attributes()->uri.
                        ">details</a>&nbsp;&nbsp;</td>";
				echo "</tr>";
			}
			echo "</table>";
			break;
		}
		
		case "product": {
				echo "<tr>";
				echo "<td>" . $array->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td><code>" . $array->code ."</code>&nbsp;&nbsp;</td>";
				echo "<td>" . $array->description . "&nbsp;&nbsp;</td>";
				//echo "<td>" . $array->family . "&nbsp;&nbsp;</td>";
				echo "<td>\$" . money_format('%i', (string)$array->sell_price) . "&nbsp;&nbsp;</td>";
				echo "<td>" . round($array->inventory->available) . "</td>";
				echo "</tr>";
			break;
		}
		
		case "customer": {
			echo '<div style="padding-left:50px; padding-top:50px;">';
			if ($array->photo->attributes()->uri)
                            echo '<table><tr><td><img src="data:image/jpeg;base64,'.displayPhoto($array->photo->attributes()->uri).'" /></td>' .
					'<td style="vertical-align:top;"><table style="padding-left:20px;">';// */
                        else {
                            echo '<table><tr><td><img src="images/customer.png" width="256px" /></td>' .
					'<td style="vertical-align:top;"><table style="padding-left:20px;">';
                                        
                                    }
                        if ($array->is_company=='true' && (string)$array->company) {
                            echo '<tr><td><span style="font-weight:bold;font-size:30px;">'.$array->company.'</span></td></tr>';
                            echo '<tr><td>&nbsp;</td></tr>';
                            echo '<tr><td><table><tr><td width="120px"><span style="color:grey;font-size:20px;">contact</span></td>'.
                                      '<td><span style="font-size:20px;">' .$array->name->first . ' ' . $array->name->last. '</span></td>'.
                                  '</tr>';
                        }
                        else
                        if ($array->name) {
                            echo '<tr><td><span style="font-weight:bold;font-size:30px;">'.$array->name->first . ' ' . $array->name->last .'</span></td></tr>';
                            echo '<tr><td>&nbsp;</td></tr>';
                            if ((string)$array->company)
                                echo '<tr><td><table><tr><td width="120px"><span style="color:grey;font-size:20px;">company</span></td>'.
                                      '<td><span style="font-size:20px;">'.$array->company.'</span></td>'.
                                        '</tr>';
			}
                        
                        if ((string)$array->phone_numbers->phone_number->type)
                            echo '<tr><td><span style="color:grey;font-size:20px;">'.$array->phone_numbers->phone_number->type.'</span></td>';
                        else
                            echo '<tr><td><span style="color:grey;font-size:20px;">phone</span></td>';
                        echo '<td><span style="font-size:20px;">'.$array->phone_numbers->phone_number->number.'</span></td></tr>';
                        
                        if ($array->email)
                            echo '<tr><td><span style="color:grey;font-size:20px;">email</span></td>'.
                                      '<td><span style="font-size:20px;"><a href="mailto:'.$array->email.'">'.$array->email.'</a></span></td></tr>';
                        
                        if ((string)$array->homepage)
                            echo '<tr><td><span style="color:grey;font-size:20px;">homepage</span></td>'.
                                     '<td><span style="font-size:20px;"><a href="'.$array->homepage.'" target="_blank">'.$array->homepage.'</a></span></td></tr>';
                        
			echo "</tr></table></td></tr></table></td></tr></table>";
			break;
		}
		
		case "customers": {
			$x = 0;
			echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\">" .
					"<td>id</td><td>name</td><td>email</td><td>phone</td><td>view details</td></tr>";
			while ($array->customer[$x]) {
				echo "<tr><td>" . $array->customer[$x]->attributes()->id . "&nbsp;&nbsp;</td>";
				echo "<td>" . $array->customer[$x]->name->first . " " . $array->customer[$x]->name->last . "&nbsp;&nbsp;</td>";
				echo "<td>" . $array->customer[$x]->email . "&nbsp;&nbsp;</td>";
				echo "<td>" . $array->customer[$x]->phone_numbers->phone_number->number . "&nbsp;&nbsp;</td>";
				echo "<td><a href=\"viewDetails.php?resource=customer&id=". $array->customer[$x]->attributes()->id .
						"\">details</a>&nbsp;&nbsp;</td></tr>";
				$x++;
			}
			break;
		}
		
		case "contacts": {
			$x = 0;
			echo "<table border=\"0\">";
			while ($array->customer_contact[$x]) {
				echo "<tr>";
				echo "<td>" . $array->customer_contact[$x]->attributes()->id . "&nbsp;&nbsp;</td>";
				echo "<td>" . $array->customer_contact[$x]->name->first . " " . $array->customer_contact[$x]->name->last . "</td>";
				echo "</tr>";
				
				$x++;
			}
			break;
		}
		
		case "classes": {
			$x = 0;
			echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\"><td>id</td><td>class name</td></tr>";
			while ($array->class[$x]) {
				echo "<tr>";
				echo "<td>" . $array->class[$x]->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->class[$x]->name . "</td>";
				echo "</tr>";
				$x++;
			}
			echo "</table>";
			break;
		}
		
		case "gift_cards": {
			echo "<tr><td>" . $array->attributes()->id ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->gift_card_id ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->totals->total ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->totals->used ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->totals->credit ."&nbsp;&nbsp;</td>";
			echo "<td>" . substr($array->created,0,10) ."&nbsp;&nbsp;</td>";
			echo "<td>" . substr($array->modified,0,10) ."&nbsp;&nbsp;</td>";
			echo "<td><a href=\"viewDetails.php?" .
					"resource=gift_card" .
					"&id=" . $array->attributes()->id .
					"\">details</a></td></tr>";
			break;
		}
		
		case "gift_card": {
			echo "<div style=\"padding-left:50px; padding-top:50px;\">";
			echo "<table><tr><td><img src=\"images/gift-card.jpeg\" /></td>" .
					"<td><table style=\"padding-left:20px;\">";
			if ($array->flags->active)
				echo "<tr><td style=\"color:#00b300;\">ACTIVE</td></tr>";
			else
				echo "<tr><td style=\"color:#ff0000;\">INACTIVE</td></tr>";
			echo "<tr><td>gift card id: " . $array->gift_card_id ."&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>serial #: " . $array->serial_number ."&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td>remaining: <b>" . $array->totals->credit ."</b>&nbsp;&nbsp;</td>";
			echo "<tr><td>used: " . $array->totals->used ."&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>start total: " . $array->totals->total ."&nbsp;&nbsp;</td></tr>" .
					"</table></td></tr>";
			echo "<tr><td style=\"padding-top:20px; padding-left:25px;\">" .
					"created: " . substr($array->created,0,10) .", " .
					substr($array->created,11,5) . "&nbsp;&nbsp;</td>";
			echo "<td style=\"padding-top:20px; padding-left:25px;\">" .
					"modified: " . substr($array->modified,0,10) .", " .
					substr($array->modified,11,5) . "&nbsp;&nbsp;</td></tr>";
			echo "<tr><td style=\"padding-left:25px;\">" .
					"invoice: <b>" . $array->invoice->attributes()->id ."</b>&nbsp;&nbsp;</td></tr>";
			echo "</table><br><br>";
			
			echo "<div style=\"padding-left:20px;\"><table border=\"1\">" .
					"<tr style=\"background-color: #b0b0b0;\">";
			echo "<td>history&nbsp;&nbsp;</td><td>note&nbsp;&nbsp;</td><td>date&nbsp;&nbsp;</td>" .
					"<td>source&nbsp;&nbsp;</td><td>before&nbsp;&nbsp;</td><td>after&nbsp;&nbsp;</td>" .
					"<td>amount&nbsp;&nbsp;</td><td>user id&nbsp;&nbsp;</td></tr>";
			$x=0;
			while ($array->history_entries->history[$x]) {
				echo "<tr>";
				echo "<td>".$array->history_entries->history[$x]->attributes()->id."&nbsp;&nbsp;</td>";
				echo "<td>".$array->history_entries->history[$x]->note."&nbsp;&nbsp;</td>";
				echo "<td>".substr($array->history_entries->history[$x]->created,0,10)."&nbsp;&nbsp;</td>";
				echo "<td>".$array->history_entries->history[$x]->source_id."&nbsp;&nbsp;</td>";
				echo "<td>".$array->history_entries->history[$x]->totals->before."&nbsp;&nbsp;</td>";
				echo "<td>".$array->history_entries->history[$x]->totals->after."&nbsp;&nbsp;</td>";
				echo "<td>".$array->history_entries->history[$x]->totals->amount."&nbsp;&nbsp;</td>";
				echo "<td>".$array->history_entries->history[$x]->user->attributes()->id."&nbsp;&nbsp;</td>";
				echo "</tr>";
				$x++;
			}
			
			echo "</table></div></div>";
			break;
		}
		
		case "tax_codes": {
			$x = 0;
			echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\">" .
					"<td>id</td><td>tax code</td>" .
					"<td>tax1</td><td>tax2</td><td>tax3</td><td>tax4</td><td>tax5</td></tr>";
			while ($array->tax_code[$x]) {
				echo "<tr><td>" . $array->tax_code[$x]->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->tax_code[$x]->name ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->tax_code[$x]->rates->tax[0] ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->tax_code[$x]->rates->tax[1] ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->tax_code[$x]->rates->tax[2] ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->tax_code[$x]->rates->tax[3] ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->tax_code[$x++]->rates->tax[4] ."&nbsp;&nbsp;</td></tr>";
			}
			echo "</table>";
			break;
		}
		
		case "terms": {
			echo "<tr><td>" . $array->attributes()->id ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->terms ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->account ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->days ."&nbsp;&nbsp;</td></tr>";
			break;
		}
		
		case "currencies": {
			echo "<tr><td>" . $array->attributes()->id ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->name ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->rate ."&nbsp;&nbsp;</td>";
			echo "<td>" . $array->symbol ."&nbsp;&nbsp;</td></tr>";
			break;
		}
                
                case "payments": {
                        $x = 0;
			echo "<table border=\"0\"><tr>";
			while ($array->payments[$x]) {
				echo "<tr>";
				echo "<td>" . $array->payments[$x++]->payment->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "</tr>";
			}
			echo "</tr></table>";
			break;
                }
                
                case "payment": {
                        echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\">" .
					"<td>id</td><td>source</td><td>method</td><td>type</td>" .
					"<td>amount</td><td>tendered</td><td>date cre</td>" .
					"<td>date mod</td></tr>";
                        echo "<tr><td>".$array->attributes()->id."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->source->attributes()->id."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->payment_method."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->type."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->amount."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->tendered."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->datetime_created."&nbsp;&nbsp;</td>";
                        echo "<td>".$array->datetime_modified."&nbsp;&nbsp;</td>";
                        echo "</tr></table>";
                        break;
                }
		
		case "users": {
			$x = 0;
			echo "<table border=\"1\">";
			echo "<tr style=\"background-color: #b0b0b0;\">" .
					"<td>id</td><td>username</td>" .
					"<td>first name</td><td>last name</td><td>email</td>" .
					"<td>privileges</td><td>active</td><td>view details</td></tr>";
			while ($array->user[$x]) {
				echo "<tr><td>" . $array->user[$x]->attributes()->id ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->user[$x]->username ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->user[$x]->name->first ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->user[$x]->name->last ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->user[$x]->email ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->user[$x]->privilege_group->name ."&nbsp;&nbsp;</td>";
				echo "<td>" . $array->user[$x]->active ."&nbsp;&nbsp;</td>";
				echo "<td><a href=\"viewDetails.php?resource=user&id=".
						$array->user[$x++]->attributes()->id."\">details</a></td></tr>";
			}
			break;
		}
		
		case "user": {
			echo "<div style=\"padding-left:50px; padding-top:50px;\">";
			echo "<table><tr><td><img src=\"images/user.png\" /></td>" .
					"<td><table style=\"padding-left:20px;\">";
			echo "<tr><td><h1>".$array->name->first." ".$array->name->last."</h1></td></tr>";
			if ($array->active)
				echo "<tr><td>status: <span style=\"color:#00b300;font-weight:bold;\">ACTIVE</span></td></tr>";
			else
				echo "<tr><td>status: <span style=\"color:#ff0000;font-weight:bold;\">INACTIVE</span></td></tr>";
			if ($array->account_locked=='true')
				echo "<tr><td>account: <span style=\"color:#ff0000;font-weight:bold;\">LOCKED</span></td></tr>";
			else
				echo "<tr><td>account: <span style=\"color:#00b300;font-weight:bold;\">ACTIVE</span></td></tr>";
			echo "<tr><td>id: <b>" . $array->attributes()->id ."</b>&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>username: <b>" . $array->username ."</b>&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>privilege: <b>" . $array->privilege_group->name ."</b>&nbsp;&nbsp;</td>";
			echo "<tr><td>phone: " . $array->phone ."&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>email: " . $array->email ."&nbsp;&nbsp;</td></tr>" .
					"</table></td></tr></table>";
			
			echo "<div style=\"padding-left:25px; padding-top:15px\"><table width=\"500\"><tr>";
			echo "<td>gsx tech id: <b>" . $array->gsx_tech_id ."</b>&nbsp;&nbsp;</td>";
			echo "<td>job product code: <b>" . $array->product_code ."</b>&nbsp;&nbsp;</td></tr>";
			echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
			echo "<tr>";
			$count=0;
			if ($array->internal_user=='true') {
				echo "<td>internal user " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($array->hidden=='true') {
				echo "<td>hidden user " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($count%2==0) echo "</tr><tr>";
			
			if ($array->enabled=='true') {
				echo "<td>can connect to LS " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($count%2==0) echo "</tr><tr>";
			
			if ($array->can_open_from_otr=='true') {
				echo "<td>can connect to OTR " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($count%2==0) echo "</tr><tr>";
			
			if ($array->display_welcome=='true') {
				echo "<td>display welcome screen on login " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($count%2==0) echo "</tr><tr>";
			
			if ($array->open_to_pos=='true') {
				echo "<td>launch POS on login " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($count%2==0) echo "</tr><tr>";
			
			if ($array->can_discount=='true') {
				echo "<td>can apply discounts " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			
			if ($count%2==0) echo "</tr><tr>";
			
			if ($array->read_eula=='true') {
				echo "<td>has read EULA " .
						"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td>";
				$count++;
			}
			//echo "<td>open to pos".$array->open_to_pos."</td>";
			echo "</tr>";
			
			if ($array->expired=='true') {
				echo "<tr><td style=\"color:#ff0000; font-weight:bold\">password expired " .
				"<input type=\"checkbox\" disabled=\"disabled\" checked=\"checked\"></td></tr>";
			}			
			
			
			echo "</table></div></div>";
			break;
		}
		
		default: { 
			echo "ERROR: Not found or Cannot parse <b>" . $obj . "</b> object yet...<br>";
			return false;
		}
	}
}

?>
