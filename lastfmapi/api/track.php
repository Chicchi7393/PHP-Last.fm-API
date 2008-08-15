<?php

class lastfmApiTrack extends lastfmApiBase {
	public $similar;
	public $topFans;
	public $topTags;
	public $searchResults;
	
	private $apiKey;
	private $track;
	private $artist;
	private $mbid;
	
	function __construct($apiKey, $track, $artist) {
		$this->apiKey = $apiKey;
		$this->track = $track;
		$this->artist = $artist;
	}
	
	public function getSimilar() {
		$vars = array(
			'method' => 'track.getsimilar',
			'api_key' => $this->apiKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			if ( count($call->similartracks->track) > 0 ) {
				$i = 0;
				foreach ( $call->similartracks->track as $track ) {
					$this->similar[$i]['name'] = (string) $track->name;
					$this->similar[$i]['match'] = (string) $track->match;
					$this->similar[$i]['mbid'] = (string) $track->mbid;
					$this->similar[$i]['url'] = (string) $track->url;
					$this->similar[$i]['streamable'] = (string) $track->streamable;
					$this->similar[$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
					$this->similar[$i]['artist']['name'] = (string) $track->artist->name;
					$this->similar[$i]['artist']['mbid'] = (string) $track->artist->mbid;
					$this->similar[$i]['artist']['url'] = (string) $track->artist->url;
					$this->similar[$i]['images']['small'] = (string) $track->image[0];
					$this->similar[$i]['images']['medium'] = (string) $track->image[1];
					$this->similar[$i]['images']['large'] = (string) $track->image[2];
					$i++;
				}
				
				return $this->similar;
			}
			else {
				$this->error['code'] = 90;
				$this->error['desc'] = 'This track has no similar tracks';
				return FALSE;
			}
		}
		elseif ( $call['status'] == 'failed' ) {
			// Fail with error code
			$this->error['code'] = $call->error['code'];
			$this->error['desc'] = $call->error;
			return FALSE;
		}
		else {
			//Hard failure
			$this->error['code'] = 0;
			$this->error['desc'] = 'Unknown error';
			return FALSE;
		}
	}
	
	public function getTags($sessionKey, $secret) {
		$vars = array(
			'method' => 'track.gettags',
			'api_key' => $this->apiKey,
			'sk' => $sessionKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		$sig = $this->apiSig($secret, $vars);
		$vars['api_sig'] = $sig;
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			if ( count($call->tags->tag) > 0 ) {
				$this->tags['artist'] = (string) $call->tags['artist'];
				$this->tags['track'] = (string) $call->tags['track'];
				$i = 0;
				foreach ( $call->tags->tag as $tag ) {
					$this->tags['tags'][$i]['name'] = (string) $tag->name;
					$this->tags['tags'][$i]['url'] = (string) $tag->url;
					$i++;
				}
				
				return $this->tags;
			}
			else {
				$this->error['code'] = 90;
				$this->error['desc'] = 'The user has no tags on this track';
				return FALSE;
			}
		}
		elseif ( $call['status'] == 'failed' ) {
			// Fail with error code
			$this->error['code'] = $call->error['code'];
			$this->error['desc'] = $call->error;
			return FALSE;
		}
		else {
			//Hard failure
			$this->error['code'] = 0;
			$this->error['desc'] = 'Unknown error';
			return FALSE;
		}
	}
	
	public function getTopFans() {
		$vars = array(
			'method' => 'track.gettopfans',
			'api_key' => $this->apiKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			if ( count($call->topfans->user) > 0 ) {
				$this->topFans['artist'] = (string) $call->topfans['artist'];
				$this->topFans['track'] = (string) $call->topfans['track'];
				$i = 0;
				foreach ( $call->topfans->user as $user ) {
					$this->topFans['users'][$i]['name'] = (string) $user->name;
					$this->topFans['users'][$i]['url'] = (string) $user->url;
					$this->topFans['users'][$i]['image']['small'] = (string) $user->image[0];
					$this->topFans['users'][$i]['image']['medium'] = (string) $user->image[1];
					$this->topFans['users'][$i]['image']['large'] = (string) $user->image[2];
					$this->topFans['users'][$i]['weight'] = (string) $user->weight;
					$i++;
				}
				
				return $this->topFans;
			}
			else {
				$this->error['code'] = 90;
				$this->error['desc'] = 'This track has no fans';
				return FALSE;
			}
		}
		elseif ( $call['status'] == 'failed' ) {
			// Fail with error code
			$this->error['code'] = $call->error['code'];
			$this->error['desc'] = $call->error;
			return FALSE;
		}
		else {
			//Hard failure
			$this->error['code'] = 0;
			$this->error['desc'] = 'Unknown error';
			return FALSE;
		}
	}
}

?>