Assignment for Twitter-Timeline Challenge by rtCamp.
-----------------------------------------------------------------------------------------------------
Part-1: User Timeline

Start => User visits the script page.
User will be asked to connect using his Twitter account using Twitter Auth.
After authentication, script will pull latest 10 tweets from his 'home' timeline.
10 tweets will be displayed using a simple jQuery-slideshow.

Part-2: Followers Timeline

Below jQuery-slideshow (in step#4 from part-1), list of 10 followers will be shown.
Also, a search followers box with Auto-suggest support. That means as soon as user starts typing, 
his followers will start showing up.
When user will click on a follower name, 10 tweets from that follower's user-timeline will be displayed 
in same jQuery-slider, without page refresh.

Part-3: Download Tweets

There will be a download button to download all tweets for logged in user.
Download can be performed in xml format. 

-----------------------------------------------------------------------------------------------------
Libraries used:
1: TwitterAPIExchange by J7mbo
2: Twitter login using oauth 1.0 by shaadomanthra (twitteroauth folder)
3: jQuery Cycle plugin made by Mike Alsup

>>> Thanks to them. Special thanks to Google, Stackoverflow...

-----------------------------------------------------------------------------------------------------
If there are errors in retrieval, it's due to Twitter API limit.
However it can retrieve 5000 followers and 3200 tweets to download which is hard limit of Twitter API.
If user has no tweets on his 'user' timeline, slider will be empty.
Similarly if user has not followed anyone or no tweets from those whom user follows, slider will be 
empty.
Once after successfully logging in, if user wants to check for another account-> clear 'cookies and 
other site data'.

-----------------------------------------------------------------------------------------------------
rtCamp is a registered trademark of rtCamp Solutions Pvt. Ltd.



EDIT:
Fixed errors showing if user has 0 followers.
Fixed file extension error.
Updated search box which was only showing followers. Now any public accounts can be searched.
Searched user's tweets can be downloaded.
