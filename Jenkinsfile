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

        stage('Run PHPUnit Tests') {
            steps {
                bat "set PATH=%PHP_PATH%;%PATH% && vendor\\bin\\phpunit tests"
            }
        }
    }

    post {
        success { echo 'Build Successful!' }
        failure { echo 'Build Failed!' }
    }
}