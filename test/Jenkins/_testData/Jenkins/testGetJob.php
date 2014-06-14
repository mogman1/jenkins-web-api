<?php
return 'HTTP/1.1 200 OK
X-Jenkins: 1.566
X-Jenkins-Session: f74c1f71
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
    },
    {

    },
    {

    },
    {

    },
    {

    },
    {

    },
    {

    },
    {

    },
    {

    },
    {

    }
  ],
  "description" : "<embed height=\"300\" src=\"http://localhost:8080/job/base/ws/build/pdepend/overview-pyramid.svg\" type=\"image/svg+xml\" width=\"500\"/>\r\n<embed height=\"300\" src=\"http://localhost:8080/job/base/ws/build/pdepend/dependencies.svg\" type=\"image/svg+xml\" width=\"500\"/>",
  "displayName" : "jenkins-web-api",
  "displayNameOrNull" : null,
  "name" : "jenkins-web-api",
  "url" : "http://jenkins/job/jenkins-web-api/",
  "buildable" : true,
  "builds" : [
    {
      "number" : 387,
      "url" : "http://jenkins/job/jenkins-web-api/387/"
    },
    {
      "number" : 386,
      "url" : "http://jenkins/job/jenkins-web-api/386/"
    },
    {
      "number" : 385,
      "url" : "http://jenkins/job/jenkins-web-api/385/"
    },
    {
      "number" : 384,
      "url" : "http://jenkins/job/jenkins-web-api/384/"
    },
    {
      "number" : 383,
      "url" : "http://jenkins/job/jenkins-web-api/383/"
    },
    {
      "number" : 325,
      "url" : "http://jenkins/job/jenkins-web-api/325/"
    }
  ],
  "color" : "red",
  "firstBuild" : {
    "number" : 325,
    "url" : "http://jenkins/job/jenkins-web-api/325/"
  },
  "healthReport" : [
    {
      "description" : "Number of checkstyle violations is 17,240",
      "iconUrl" : "health-00to19.png",
      "score" : 0
    },
    {
      "description" : "Build stability: All recent builds failed.",
      "iconUrl" : "health-00to19.png",
      "score" : 0
    }
  ],
  "inQueue" : false,
  "keepDependencies" : false,
  "lastBuild" : {
    "number" : 387,
    "url" : "http://jenkins/job/jenkins-web-api/387/"
  },
  "lastCompletedBuild" : {
    "number" : 387,
    "url" : "http://jenkins/job/jenkins-web-api/387/"
  },
  "lastFailedBuild" : {
    "number" : 387,
    "url" : "http://jenkins/job/jenkins-web-api/387/"
  },
  "lastStableBuild" : null,
  "lastSuccessfulBuild" : {
    "number" : 325,
    "url" : "http://jenkins/job/jenkins-web-api/325/"
  },
  "lastUnstableBuild" : {
    "number" : 325,
    "url" : "http://jenkins/job/jenkins-web-api/325/"
  },
  "lastUnsuccessfulBuild" : {
    "number" : 387,
    "url" : "http://jenkins/job/jenkins-web-api/387/"
  },
  "nextBuildNumber" : 388,
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
  "concurrentBuild" : false,
  "downstreamProjects" : [

  ],
  "scm" : {

  },
  "upstreamProjects" : [

  ]
}';