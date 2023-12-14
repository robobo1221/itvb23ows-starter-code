pipeline {
    agent any

    environment {
        DOCKER_IMAGE_PHP = 'ow-start:latest'
        DOCKER_IMAGE_MYSQL = 'mysql:latest'
    }

    stages {
        stage('Build PHP Image') {
            steps {
                script {
                    docker.build(DOCKER_IMAGE_PHP, '-f Dockerfile .')
                }
            }
        }

        stage('Test PHP Image') {
            steps {
                script {
                    docker.image(DOCKER_IMAGE_PHP).inside {
                        sh 'php --version'
                    }
                }
            }
        }

        stage('Test MySQL Container') {
            steps {
                script {
                    docker.image(DOCKER_IMAGE_MYSQL).inside {
                        sh "mysql --version"
                    }
                }
            }
        }

        stage('Run Docker Containers') {
            steps {
                script {
                    sh "docker-compose -f docker-compose.yml up -d"
                }
            }
        }
    }

    post {
        always {
            script {
                sh "docker-compose -f docker-compose.yml down"
            }
        }
    }
}