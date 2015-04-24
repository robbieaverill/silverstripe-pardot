<?php
class PardotTracker extends SiteTreeExtension {

	/**
	*gets tracking code based on campaign
	*@return tracking javascript for pardot api
	*/
	public static function GetPardotTrackingJs()
	{
		$html = false;
		$campaign = PardotConfig::getCampaignCode();
		
		if($campaign)
		{
			$tracker_cache = SS_Cache::factory('Pardot');
			if(!$tracking_code_template = $tracker_cache->load('pardot_tracking_code_template'))
			{
				$api_credentials = PardotConfig::getPardotCredentials();
				$pardot = new Pardot_API();
				if(!$pardot->is_authenticated())
					$pardot->authenticate($api_credentials);
				
				$account = $pardot->get_account();

				if ( isset( $account->tracking_code_template ) )
				{
					$tracking_code_template = $account->tracking_code_template;
					$tracker_cache->save($tracking_code_template,'pardot_tracking_code_template');
				}
			}
		$tracking_code_template = str_replace( '%%CAMPAIGN_ID%%', $campaign+1000, $tracking_code_template );
		error_log($tracking_code_template);
		$campaign = $campaign + 1000; 
		$html =<<<HTML
<script> type="text/javascript">
piCId = '{$campaign}';
{$tracking_code_template}
</script>
HTML;

		}
		return $html;
	}

	public static function cacheFormsByFormName()
    {
	  	$pardot = new Pardot_API(PardotConfig::getPardotCredentials());
	  	$forms = $pardot->get_forms();

	  	$pardot_cache = SS_Cache::factory('Pardot');
	  	$pardot_cache->save(serialize($forms),'serialized_forms');

	  	return print_r($forms,1);

    }

}