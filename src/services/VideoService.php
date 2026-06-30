<?php

namespace ClinicManagement\Services;

class VideoService
{
    /**
     * Generate the Jitsi Meet URL.
     * In a production environment, this should generate a JWT for authentication.
     *
     * @param string $roomName
     * @param string $userName
     * @return string
     */
    public function generateMeetingUrl(string $roomName, string $userName): string
    {
        $domain = 'meet.jit.si'; // Or a custom self-hosted Jitsi domain
        
        // Build the URL with the user's name prefilled
        $url = "https://{$domain}/{$roomName}#userInfo.displayName=" . urlencode($userName);
        
        return $url;
    }
}
