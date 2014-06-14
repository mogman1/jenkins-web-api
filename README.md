Jenkins Web API
===============
[![Build Status](https://travis-ci.org/mogman1/jenkins-web-api.svg?branch=master)](https://travis-ci.org/mogman1/jenkins-web-api)

I only noticed, somewhat sheepishly, that there were two other Jenkins PHP libraries 
already out there by the time I finished my work on this.  Admittedly, at present this 
library doesn't break any new ground, it provides an API for reading the most common 
data from Jenkins in addition to being able to trigger remote builds.  Future releases 
already in the works will allow creating new objects on Jenkins (like jobs), as well as 
reading them.

Currently, the following data objects are supported:

- Job
- Build
- Node
- Queue Item
- View

Installation
------------
This package can be installed using composer:

```
  "require": {
    "mogman1/jenkins-web-api": "1.*"
  }
```

Usage
-----
All implementations of ApiObject use the Jenkins class to communicate with your Jenkins 
server, with the actual connection information managed by the Http class:

```php
use mogman1\Jenkins\Jenkins
use mogman1\Jenkins\Server\Http
  
$http = new Http("http://jenkins-server", "jenkinsUsername", "userAccessToken");
$jenkins = new Jenkins($http);
```

The library assumes that Jenkins' CSRF crumb tokens are being used and automatically 
fetches a crumb for each request (a future release will check if crumbs are being used
and change behaviour accordingly).

The Jenkins class can be used to fetch top-level information, such as info on the 
Views available, the Jenkins Node, or available Jobs.

```php
$node = $jenkins->getNodeInfo();
foreach ($node->jobs as $job) {
  echo $job->name."\n";
  echo $job->url."\n";
  echo $job->color."\n";
}
```

When objects are returned as part of information obtained from other objects, like 
the jobs returned from the Node object, they are usually in reduced-info state to 
minimize calls to Jenkins for information until you really need it.  The get all 
fields available for that object, or simply to fetch the latest from the server, use 
the update() method:

```php
$job->update();
//access to additional fields, such as past builds
foreach ($job->builds as $build) {
  echo $build->number."\n";
}
```

You can also directly fetch a job you already know the name of, which will come back 
with all information already loaded (no need to call update()).

```php
$job = $jenkins->getJob("jenkins-web-api");
//go crazy
```

Job objects are also capable of triggering builds.  If you have parameters enabled for 
your job, you can pass them in as an array.  If your job requires the additional 
authentication token, you'll need to pass this in as one of the parameters.  The return 
value here is a QueueItem because Jenkins doesn't create an actual build right away 
(see [this comment](https://issues.jenkins-ci.org/browse/JENKINS-12827?focusedCommentId=201381&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-201381) from the Jenkins issue tracker).  

Getting information on the build you triggered can require some shenanigans.  Generally 
a queue item is stuck in a Jenkins queue for a several seconds before being acted on by
Jenkins, so you'll have to poll this object until the build becomes available under
QueueItem::$executable.  However, please note that eventually queued items get removed 
from the Jenkins queue and you won't be able to fetch data on it anymore (you'll get a 
404 response which triggers an exception).  My experience is that it you have plenty of 
time before it goes away, but keep that in mind.

```php
$queueItem = $job->triggerBuild(array('token' => "secret", 'param1' => "val1"));
$build = null;
try {
  while (!$queueItem->executable) {
    $queueItem->update()
    sleep(1);
  }
  
  $build = $queueItem->executable;
} catch (JenkinsConnectionException $e) {
  //couldn't get build info
}

echo $build->getConsoleLog();
```

Finally, you can run a query directly against the Jenkins server with the get() method
on the Jenkins class.  The first parameter is the path to query on the server, and the
second is an associative array of any parameters you might wish to submit with your
request.  The return type is HttpResponse:

```php
$httpResponse = $jenkins->get("/job/your-job", array());
echo $httpResponse->getBody();
```

For everything else, take a look at the code.  All methods and properties have been
commented to explain what they do (to the best of my knowledge at the time).  Please
feel free to get in contact with me for feature requests, bug fixes, or anything else.

Looking Forward
---------------
There are a few areas that I know will have interface-breaking changes sooner than later:

- Users
- Health Report (Job)
- Action (multiple classes)
- Property (Job)

Basically any place where an array is being returned, I'm hoping to be able to convert 
into full-on objects once I understand the what's being returned from Jenkins better.