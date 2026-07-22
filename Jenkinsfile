pipeline {
    agent any

    environment {
        PHP_PATH = 'C:\\xampp8.2\\php'
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                bat "set PATH=%PHP_PATH%;%PATH% && composer install"
            }
        }

        stage('Grype Scan') {
            steps {
                bat """
                    cd "%WORKSPACE%"
                    grype dir:. --output json > grype-report.json
                """
            }
        }

        stage('PHP CodeSniffer') {
            steps {
                bat """
                    set PATH=%PHP_PATH%;%PATH%
                    vendor\\bin\\phpcs --standard=PSR12 src ^
                      --runtime-set ignore_errors_on_exit 1 ^
                      --runtime-set ignore_warnings_on_exit 1 ^
                      --report=full > phpcs-report.txt
                """
            }
        }

        stage('Run PHPUnit Tests') {
            steps {
                bat "set PATH=%PHP_PATH%;%PATH% && vendor\\bin\\phpunit tests"
            }
        }
    }

    post {
        always {
            archiveArtifacts artifacts: 'grype-report.json, phpcs-report.txt', fingerprint: true
        }
        success {
            echo 'Build Successful!'
        }
        failure {
            echo 'Build Failed!'
        }
    }
}