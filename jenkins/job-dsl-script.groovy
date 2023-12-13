pipelineJob('AutomatedPipeline') {
    definition {
        cpsScm {
            scm {
                git {
                    remote {
                        url('https://github.com/robobo1221/itvb23ows-starter-code')
                    }
                    branches('*/master')
                }
            }
            scriptPath('Jenkinsfile')
        }
    }
}