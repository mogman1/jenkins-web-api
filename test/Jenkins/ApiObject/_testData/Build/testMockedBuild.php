<?php
return 'HTTP/1.1 200 OK
X-Jenkins: 1.567
X-Jenkins-Session: 8e1995d3
Content-Type: application/json;charset=UTF-8
Content-Length: 10461
Server: Jetty(8.y.z-SNAPSHOT)

{
  "actions" : [
    {
      "causes" : [
        {
          "shortDescription" : "Started by an SCM change"
        }
      ]
    },
    {
      "failCount" : 0,
      "skipCount" : 0,
      "totalCount" : 214,
      "urlName" : "testReport"
    }
  ],
  "artifacts" : [

  ],
  "building" : true,
  "description" : "message!",
  "duration" : 1095396,
  "estimatedDuration" : 555548,
  "executor" : null,
  "fullDisplayName" : "jenkins-web-api #123",
  "id" : "2013-09-08_00-23-08",
  "keepLog" : true,
  "number" : 123,
  "result" : "UNSTABLE",
  "timestamp" : 1378599788000,
  "url" : "http://jenkins/job/jenkins-web-api/123/",
  "builtOn" : "urmom",
  "changeSet" : {
    "items" : [
      {
        "affectedPaths" : [
          "file1",
          "file2"
        ],
        "commitId" : "123abc",
        "timestamp" : 1378598000,
        "author" : {
          "absoluteUrl" : "http://jenkins/user/sigma",
          "fullName" : "Shaun Carlson"
        },
        "comment" : "Bogus message.\n",
        "date" : "1970-01-16T22:56:38+0000 -0600",
        "id" : "123abc",
        "msg" : "Bogus message.",
        "paths" : [
          {
            "editType" : "add",
            "file" : "file1"
          },
          {
            "editType" : "edit",
            "file" : "file2"
          }
        ]
      }
    ],
    "kind" : "git"
  },
  "culprits" : [
    {
      "absoluteUrl" : "http://jenkins/user/shaun.carlson",
      "fullName" : "shaun.carlson"
    },
    {
      "absoluteUrl" : "http://jenkins/user/sigma",
      "fullName" : "Shaun Carlson"
    }
  ]
}';