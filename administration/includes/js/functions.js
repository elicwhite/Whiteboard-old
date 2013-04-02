// Redirects to a specific url after 5 seconds.
function redir(url, timeout)
{
	setTimeout(function()
    	{
    		window.location = url;
    	}, timeout);
}
