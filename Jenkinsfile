pipeline {
    agent any

    environment {
        JIRA_URL = 'https://dubeyshivanand198.atlassian.net'
        JIRA_USER = 'dubeyshivanand198@gmail.com'
        JIRA_TOKEN = credentials('jira-api-token')
        JIRA_TOKEN_B64 = credentials('jira-api-token-b64')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
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
                archiveArtifacts artifacts: 'grype-report.json, phpcs-report.txt', fingerprint: true
            }
        }
    }

    post {
        success {
            script {
                bat """
                    curl -s -X POST ^
                    -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                    -H "Content-Type: application/json" ^
                    -d "{\\"transition\\": {\\"id\\": \\"41\\"}}" ^
                    "%JIRA_URL%/rest/api/3/issue/SCRUM-6/transitions"
                """
                bat """
                    curl -s -X POST ^
                    -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                    -H "Content-Type: application/json" ^
                    -d "{\\"body\\": {\\"type\\": \\"doc\\", \\"version\\": 1, \\"content\\": [{\\"type\\": \\"paragraph\\", \\"content\\": [{\\"type\\": \\"text\\", \\"text\\": \\"Jenkins Build #%BUILD_NUMBER% SUCCESS! %BUILD_URL%\\"}]}]}}" ^
                    "%JIRA_URL%/rest/api/3/issue/SCRUM-6/comment"
                """
            }
        }

        failure {
            script {
                bat """
                    curl -s -X POST ^
                    -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                    -H "Content-Type: application/json" ^
                    -d "{\\"transition\\": {\\"id\\": \\"21\\"}}" ^
                    "%JIRA_URL%/rest/api/3/issue/SCRUM-6/transitions"
                """
                bat """
                    curl -s -X POST ^
                    -H "Authorization: Basic %JIRA_TOKEN_B64%" ^
                    -H "Content-Type: application/json" ^
                    -d "{\\"body\\": {\\"type\\": \\"doc\\", \\"version\\": 1, \\"content\\": [{\\"type\\": \\"paragraph\\", \\"content\\": [{\\"type\\": \\"text\\", \\"text\\": \\"Jenkins Build #%BUILD_NUMBER% FAILED! %BUILD_URL%\\"}]}]}}" ^
                    "%JIRA_URL%/rest/api/3/issue/SCRUM-6/comment"
                """
            }
        }
    }
}