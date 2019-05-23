FROM openjdk:7-jre-alpine

MAINTAINER Andrea Cristaudo <andrea.cristaudo@gmail.com>

RUN wget -q http://nilhcem.github.com/FakeSMTP/downloads/fakeSMTP-latest.zip \
    && mkdir /opt \
    && unzip fakeSMTP-latest.zip -d /opt \
    && rm fakeSMTP-latest.zip \
    && addgroup -S smtp -g 1000 \
    && adduser -S -G smtp -u 1000 smtp

EXPOSE 2525

VOLUME ["/var/mail"]

CMD java -jar /opt/fakeSMTP-2.0.jar -s -b -p 2525 -o /var/mail

USER smtp
