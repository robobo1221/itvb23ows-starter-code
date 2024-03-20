pipeline {
    agent any

    environment {
        DOCKER_IMAGE_PHP = 'ow-start:latest'
        DOCKER_IMAGE_MYSQL = 'mysql:latest'
    }

    stages {
        stage('SonarQube') {
            steps {
                script { scannerHome = tool 'SonarQube Scanner' }
                withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=OWS"
                }
            }
        }

        stage('Build and Run Docker Compose') {
            steps {
                script {
                    dockerComposeBuild = "docker-compose -f docker-compose.yml --profile hive build"
                    dockerComposeUp = "docker-compose -f docker-compose.yml --profile hive up -d"

                    sh "${dockerComposeBuild}"
                    sh "${dockerComposeUp}"
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

        stage('Test PHP Container') {
            steps {
                script {
                    docker.image(DOCKER_IMAGE_PHP).inside {
                        sh 'php --version'
                    }
                }
            }
        }
        
        stage('Unit Test') {
            steps {
                script {
                    docker.image(DOCKER_IMAGE_PHP).inside {
                        sh './vendor/bin/phpunit'
                    }
                }
            }
        }
    }

    //post {
    //    always {
    //        // Clean up Docker Compose containers
    //        script {
    //            sh "docker-compose -f docker-compose.yml down"
    //        }
    //    }
    //}
}