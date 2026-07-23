pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install'
            }
        }

        stage('Security Scan - Grype') {
            steps {
                sh 'grype . -o json > grype-report.json || true'
            }
        }

        stage('Code Quality - PHPCS') {
            steps {
                sh 'phpcs --report=full > phpcs-report.txt || true'
            }
        }

        stage('Unit Tests - PHPUnit') {
            steps {
                sh 'phpunit'
            }
        }

        stage('Archive Artifacts') {
            steps {
                archiveArtifacts artifacts: 'grype-report.json, phpcs-report.txt', fingerprint: true
            }
        }
    }

    post {
        always {
            script {
                def jiraIssueKeys = []

                def logOutput = sh(
                    script: "git log -n 10 --pretty=format:%s",
                    returnStdout: true
                ).trim()

                logOutput.readLines().each { msg ->
                    def matcher = (msg =~ /[A-Z]+-\\d+/)
                    matcher.each { key ->
                        if (!jiraIssueKeys.contains(key)) {
                            jiraIssueKeys << key
                        }
                    }
                }

                if (jiraIssueKeys.isEmpty()) {
                    echo "No Jira issue keys found in recent commit messages."
                } else {
                    echo "Detected Jira issues: ${jiraIssueKeys}"

                    def statusText = currentBuild.result ?: 'SUCCESS'

                    jiraIssueKeys.each { issueKey ->
                        def commentText = "Build ${env.BUILD_NUMBER} ${statusText} on Jenkins job ${env.JOB_NAME} " +
                                          "for commit ${env.GIT_COMMIT}. " +
                                          "Branch: ${env.GIT_BRANCH ?: 'main'}."

                        jiraAddComment(
                            idOrKey: issueKey,
                            comment: commentText
                            // Agar Jira Steps config me 'site' naam diya hai, to:
                            // site: 'Jira-Cloud'
                        )
                    }
                }
            }
        }
    }
}