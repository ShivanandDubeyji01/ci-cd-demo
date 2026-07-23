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
                // Composer install on Windows
                bat 'composer install'
            }
        }

        stage('Security Scan - Grype') {
            steps {
                // Grype scan; error ignore so pipeline continues
                bat 'grype . -o json > grype-report.json || exit /b 0'
            }
        }

        stage('Code Quality - PHPCS') {
            steps {
                // PHPCS report; non‑zero exit ignore
                bat 'phpcs --report=full > phpcs-report.txt || exit /b 0'
            }
        }

        stage('Unit Tests - PHPUnit') {
            steps {
                bat 'phpunit'
            }
        }

        stage('Archive Artifacts') {
            steps {
                archiveArtifacts artifacts: 'grype-report.json, phpcs-report.txt', fingerprint: true
            }
        }
    }

    /*
     * Jira auto-detect & comment (Windows):
     *  - Git commit messages se Jira issue keys (e.g. SCRUM-3) nikalta hai
     *  - Har detected issue pe build status comment add karta hai
     */
    post {
        always {
            script {
                def jiraIssueKeys = []

                // Windows: git log via bat
                def logOutput = bat(
                    script: 'git log -n 10 --pretty=format:"%s"',
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

                        // Jira plugin pipeline step
                        jiraAddComment(
                            idOrKey: issueKey,
                            comment: commentText
                            // Agar Jira Steps config me koi 'site' name diya hai,
                            // to yahan: site: 'Your-Site-Name'
                        )
                    }
                }
            }
        }
    }
}