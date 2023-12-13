pipeline {
    agent none
    stages {
        stage('PHP') {
            agent {
                docker { image 'ow-start:latest' }
            }
            steps {
                sh 'php --version'
            }
        }
        stage('DB') {
            agent {
                docker { image 'mysql:latest' }
            }
            steps {
                sh 'mysql --version'
            }
        }
    }
}