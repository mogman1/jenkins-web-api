<?php
return 'HTTP/1.1 200 OK
X-Jenkins: 1.567
X-Jenkins-Session: 8e1995d3
Content-Type: application/json;charset=UTF-8
Content-Length: 3991
Server: Jetty(8.y.z-SNAPSHOT)

{
  "actions" : [
    {
      "parameterDefinitions" : [
        {
          "defaultParameterValue" : {
            "value" : "next_release"
          },
          "description" : "",
          "name" : "branch",
          "type" : "ChoiceParameterDefinition",
          "choices" : [
            "next_release",
            "deployed_production"
          ]
        },
        {
          "defaultParameterValue" : {
            "value" : "build"
          },
          "description" : "",
          "name" : "build_target",
          "type" : "ChoiceParameterDefinition",
          "choices" : [
            "build",
            "package"
          ]
        }
      ]
    }
  ],
  "description" : "Jello world",
  "displayName" : "jenkins-web-api",
  "displayNameOrNull" : null,
  "name" : "jenkins-web-api",
  "url" : "http://jenkins/job/jenkins-web-api/",
  "buildable" : true,
  "builds" : [
    {
      "number" : 440,
      "url" : "http://jenkins/job/jenkins-web-api/440/"
    },
    {
      "number" : 439,
      "url" : "http://jenkins/job/jenkins-web-api/439/"
    }
  ],
  "color" : "potato",
  "firstBuild" : {
    "number" : 325,
    "url" : "http://jenkins/job/jenkins-web-api/325/"
  },
  "healthReport" : [
    {
      "description" : "Number of checkstyle violations is 17,186",
      "iconUrl" : "health-00to19.png",
      "score" : 0
    },
    {
      "description" : "Build stability: All recent builds failed.",
      "iconUrl" : "health-00to19.png",
      "score" : 0
    }
  ],
  "inQueue" : true,
  "keepDependencies" : true,
  "lastBuild" : {
    "number" : 440,
    "url" : "http://jenkins/job/jenkins-web-api/440/"
  },
  "lastCompletedBuild" : {
    "number" : 440,
    "url" : "http://jenkins/job/jenkins-web-api/440/"
  },
  "lastFailedBuild" : {
    "number" : 440,
    "url" : "http://jenkins/job/jenkins-web-api/440/"
  },
  "lastStableBuild" : {
    "number" : 439,
    "url" : "http://jenkins/job/jenkins-web-api/439/"
  },
  "lastSuccessfulBuild" : {
    "number" : 325,
    "url" : "http://jenkins/job/jenkins-web-api/325/"
  },
  "lastUnstableBuild" : {
    "number" : 325,
    "url" : "http://jenkins/job/jenkins-web-api/438/"
  },
  "lastUnsuccessfulBuild" : {
    "number" : 440,
    "url" : "http://jenkins/job/jenkins-web-api/440/"
  },
  "nextBuildNumber" : 441,
  "property" : [
    {
      "parameterDefinitions" : [
        {
          "defaultParameterValue" : {
            "name" : "branch",
            "value" : "next_release"
          },
          "description" : "",
          "name" : "branch",
          "type" : "ChoiceParameterDefinition",
          "choices" : [
            "next_release",
            "deployed_production"
          ]
        },
        {
          "defaultParameterValue" : {
            "name" : "build_target",
            "value" : "build"
          },
          "description" : "",
          "name" : "build_target",
          "type" : "ChoiceParameterDefinition",
          "choices" : [
            "build",
            "package"
          ]
        }
      ]
    }
  ],
  "queueItem" : null,
  "concurrentBuild" : true,
  "downstreamProjects" : [

  ],
  "scm" : {

  },
  "upstreamProjects" : [

  ]
}';