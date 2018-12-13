<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
	  	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
	 	<meta name="format-detection" content="telephone=no"/>
	    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500,500i,700,700i" rel="stylesheet">
		<style>
			/* Reset styles */ 
			body { margin: 0; padding: 0; min-width: 100%; width: 100% !important; height: 100% !important; font-family: 'Rubik', sans-serif !important;}
			body, table, td, div, p, a { -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%; }
			table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse !important; border-spacing: 0; }
			img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
			#outlook a { padding: 0; }
			.ReadMsgBody { width: 100%; } .ExternalClass { width: 100%; }
			.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }

			/* Rounded corners for advanced mail clients only */ 
			@media all and (min-width: 560px) {
				.container { border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; -khtml-border-radius: 8px;}
			}

			/* Set color for auto links (addresses, dates, etc.) */ 
			a, a:hover {
				color: #127DB3;
			}
			.footer a, .footer a:hover {
				color: #999999;
			}
 		</style>
		<!-- MESSAGE SUBJECT -->
		<title>HelpNow Email</title>
	</head>
	<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0" marginwidth="0" marginheight="0" width="100%" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%; height: 100%; -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%; background-color: #F0F0F0; color: #000000;" bgcolor="#F0F0F0" text="#000000">
		<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%;" class="background">
			<tr>
				<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;" bgcolor="#F0F0F0">

					<table border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#FFFFFF" width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit; max-width: 560px; margin-top:6vw; " class="container">
						<tr>
							<td align="center" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 3.25%; padding-right: 3.25%; width: 87.5%; text-align:center; font-size: 18px; font-weight: Normal; line-height: 130%;padding-top: 25px;color: #000000;font-family: 'Rubik', sans-serif !important; class="header">
								<img border="0" vspace="0" hspace="0" style="padding: 0; vertical-align:middle; margin: 0; margin-right:15px; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;color: #000000;" src="http://techprostudio.com/projects/helpnow/html/images/logo.png" alt="Logo" title="Logo"
								 /> 
			                    <br><br>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 17px; font-weight: 400; line-height: 160%; padding-top: 25px; color: #000000; font-family: 'Rubik', sans-serif !important;" class="paragraph">
								@if($user_details->personal_info)
									Hi {{ $user_details->personal_info->first_name }},
								@else 
									Hi there,
								@endif
							</td>
			      		</tr>
			        	<tr>
							<td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 17px; font-weight: 400; line-height: 160%;padding-top: 25px; color: #000000; font-family: 'Rubik', sans-serif !important;" class="paragraph">
								We've received a request to reset your password. If you didn't make the request. just ignore this email. Otherwise, you can reset your password using this link:
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; padding-top: 25px; padding-bottom: 5px;" class="button">
								<table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 100%; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;">
									<tr>
										<td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: none; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;" bgcolor="#00CCFF">
											<a  style="text-decoration: none; color: #FFFFFF; font-family: 'Rubik', sans-serif !important; font-size: 17px; font-weight: 400; line-height: 120%;" href="{{ $reset_link }}">
												Click here to reset your password
											</a>
										</td>
			        				</tr>
    							</table>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
								padding-top: 65px;" class="line"><hr
								color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 17px; font-weight: 400; line-height: 160%;
								padding-top: 20px;
								padding-bottom: 25px;
								color: #000000;
								font-family: 'Rubik', sans-serif !important;" class="paragraph">
									Have a&nbsp;question? <a href="mailto:support@ourteam.com" target="_blank" style="color: #127DB3; font-family: 'Rubik', sans-serif !important; font-size: 17px; font-weight: 400; line-height: 160%;">support@helpnow.com</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>