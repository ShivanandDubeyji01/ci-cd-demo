pipeline {
    agent any

    environment {
        JIRA_URL        = 'https://dubeyshivanand198.atlassian.net'
        JIRA_USER       = 'dubeyshivanand198@gmail.com'
        JIRA_TOKEN_B64  = credentials('jira-api-token-b64')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Extract Jira Issue Key') {
            steps {
                script {
                    // Commit message se automatically issue key nikalo
                    def commitMsg = bat(
                        script: 'git log -1 --pretty=%%s',
                        returnStdout: true
                    ).trim()
                    
                    def matcher = commitMsg =~ /([A-Z]+-\d+)/
                    if (matcher.find()) {
                        env.JIRA_ISSUE = matcher[0][1]
                        echo "Found Jira Issue: ${env.JIRA_ISSUE}"
                    } else {
                        env.JIRA_ISSUE = ''
                        echo "No Jira Issue key found in commit message"
                    }
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                bat 'composer install || exit /b 0'
            }
        }

        stage('Security Scan - Grype') {
            steps {
                bat 'grype . -o json > grype-report.json || exit /b 0'
            }
        }

        stage('Code Quality - PHPCS') {
            steps {
                bat 'phpcs --report=full > phpcs-report.txt || exit /b 0'
            }
        }

        stage('Unit Tests - PHPUnit') {
            steps {
                bat 'phpunit || exit /b 0'
            }
        }

        stage('Archive Artifacts') {
            steps {
                archiveArtifacts artifacts: 'grype-report.json, phpcs-report.txt',
                                 fingerprint: true
            }
        }
    }

    post {
        success {
            script {
                if (env.JIRA_ISSUE) {
                    // Auto transition → Resolved
                    bat """
                        curl -s -o NUL -w "Transition Status: %%{http_code}" ^
                        -X POST ^
                        -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                        -H "Content-Type: application/json" ^
                        -d "{\\"transition\\": {\\"id\\": \\"41\\"}}" ^
                        "%JIRA_URL%/rest/api/3/issue/%JIRA_ISSUE%/transitions"
                    """
                    // Comment add karo
                    bat """
                        curl -s -o NUL -w "Comment Status: %%{http_code}" ^
                        -X POST ^
                        -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                        -H "Content-Type: application/json" ^
                        -d "{\\"body\\": {\\"type\\": \\"doc\\", \\"version\\": 1, \\"content\\": [{\\"type\\": \\"paragraph\\", \\"content\\": [{\\"type\\": \\"text\\", \\"text\\": \\"✅ Build #%BUILD_NUMBER% SUCCESS! %BUILD_URL%\\"}]}]}}" ^
                        "%JIRA_URL%/rest/api/3/issue/%JIRA_ISSUE%/comment"
                    """
                } else {
                    echo "No Jira issue key found — skipping Jira update"
                }
            }
        }

        failure {
            script {
                if (env.JIRA_ISSUE) {
                    // Auto transition → In Progress
                    bat """
                        curl -s -o NUL -w "Transition Status: %%{http_code}" ^
                        -X POST ^
                        -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                        -H "Content-Type: application/json" ^
                        -d "{\\"transition\\": {\\"id\\": \\"21\\"}}" ^
                        "%JIRA_URL%/rest/api/3/issue/%JIRA_ISSUE%/transitions"
                    """
                    // Failure comment
                    bat """
                        curl -s -o NUL -w "Comment Status: %%{http_code}" ^
                        -X POST ^
                        -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                        -H "Content-Type: application/json" ^
                        -d "{\\"body\\": {\\"type\\": \\"doc\\", \\"version\\": 1, \\"content\\": [{\\"type\\": \\"paragraph\\", \\"content\\": [{\\"type\\": \\"text\\", \\"text\\": \\"❌ Build #%BUILD_NUMBER% FAILED! Check: %BUILD_URL%\\"}]}]}}" ^
                        "%JIRA_URL%/rest/api/3/issue/%JIRA_ISSUE%/comment"
                    """
                } else {
                    echo "No Jira issue key found — skipping Jira update"
                }
            }
        }
    }
}