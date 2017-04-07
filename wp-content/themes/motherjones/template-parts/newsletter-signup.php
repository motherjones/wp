<?php
/**
 * A signup form for newsletters, meant to go at the bottom of articles
 *
 * @package WordPress
 * @subpackage Mother_Jones
 * @since Mother Jones 1.0
 */

?>
<div class="newsletter-signup">
	<h5>Get the scoop, &nbsp;straight from Mother Jones.</h5>
	<form action="https://api.maropost.com/accounts/585/forms/3289/subscribe/177d8ba3b7fe7d39e28dcba73123eeffbd01878b" method="post" style="margin: 0 auto;" onsubmit="return MJ_check_email(this);">
	<table summary="Mother Jones Newsletter Sign Up Form" cellpadding="0" cellspacing="0">
		<tbody>
		<tr>
			<td>
			<input include_blank="true" start_year="1950" type="hidden" name="custom_fields[outreach_affiliate_code]" id="custom_fields_outreach_affiliate_code" value="Article_Bottom" />
			<input include_blank="true" start_year="1950" type="hidden" name="custom_fields[signup_url]" id="signup_url" value="" />
			<input type="hidden" value="" id="email_field" name="email_const_mp" />
												<i class="icon-envelope"></i>
			<input include_blank="true" start_year="1950" name="contact_fields[email]" id="cons_email" placeholder="ENTER YOUR EMAIL" type="text" />
			</td>
			<td>
			<input id="newsletter-signup" type="submit" name="commit" id="submit" value="Submit" />
			</td>
		</tr>
		</tbody>
	</table>
</form>
</div>
