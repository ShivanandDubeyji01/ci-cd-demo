pipeline {
    agent any

    options {
        ansiColor('xterm')
    }

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
                // Grype scan report as JSON, failure ignore so pipeline aage chale
                sh 'grype . -o json > grype-report.json || true'
            }
        }

        stage('Code Quality - PHPCS') {
            steps {
                // PHPCS report, non‑zero exit ko ignore (|| true) so that pipeline continues
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

    /*
     * Jira auto-detect & comment:
     *  - Git commit messages se Jira issue keys (e.g. SCRUM-3, PROJ-1) nikalta hai
     *  - Har detected issue pe build status comment add karta hai
     */
    post {
        always {
            script {
                // Unique Jira issue keys list
                def jiraIssueKeys = []

                // Recent commit messages (last 10) read karo
                def logOutput = sh(
                    script: "git log -n 10 --pretty=format:%s",
                    returnStdout: true
                ).trim()

                // Har commit message me Jira key regex se match
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

                    // Build result (SUCCESS/FAILURE/UNSTABLE, etc.)
                    def statusText = currentBuild.result ?: 'SUCCESS'

                    jiraIssueKeys.each { issueKey ->
                        def commentText = "Build ${env.BUILD_NUMBER} ${statusText} on Jenkins job ${env.JOB_NAME} " +
                                          "for commit ${env.GIT_COMMIT}. " +
                                          "Branch: ${env.GIT_BRANCH ?: 'main'}."

                        // Jira plugin pipeline step
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