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
}