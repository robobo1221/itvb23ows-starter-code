FROM jenkins/jenkins:lts

VOLUME /var/jenkins_home
ENV JAVA_OPTS -Djenkins.install.runSetupWizard=false
ENV CASC_JENKINS_CONFIG /var/jenkins_home/casc.yaml
COPY plugins.txt /usr/share/jenkins/ref/plugins.txt
RUN jenkins-plugin-cli --plugin-file /usr/share/jenkins/ref/plugins.txt
COPY casc.yaml /var/jenkins_home/casc.yaml

USER root
RUN apt-get update && \
    apt-get install -y docker.io && \
    rm -rf /var/lib/apt/lists/* && \
    usermod -aG docker jenkins
USER jenkins

#COPY job-dsl-script.groovy /usr/share/jenkins/ref/init.groovy.d/

EXPOSE 8080
EXPOSE 50000

CMD ["/usr/local/bin/jenkins.sh"]