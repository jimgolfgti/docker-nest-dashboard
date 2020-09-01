To update secret
1. Log in to https://home.nest.com
1. Download https://home.nest.com/session as `session.json`
1. kubectl delete secret nest-session
1. kubectl create secret generic nest-session --from-file=session.json
1. done