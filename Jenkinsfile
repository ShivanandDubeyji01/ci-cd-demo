pipeline {
    agent any
    
    environment {
        JIRA_URL = 'https://dubeyshivanand198.atlassian.net'
        JIRA_USER = 'dubeyshivanand198@gmail.com'  // apna email
        JIRA_TOKEN = credentials('jira-api-token')
    }
    
    stages {
        // ... tere existing stages same rahenge ...
    }
    
    post {
        success {
            script {
                // Issue → Resolved (Done)
                sh """
                    curl -s -X POST \
                    -H 'Authorization: Basic \$(echo -n "${JIRA_USER}:${JIRA_TOKEN}" | base64)' \
                    -H 'Content-Type: application/json' \
                    -d '{"transition": {"id": "41"}}' \
                    '${JIRA_URL}/rest/api/3/issue/SCRUM-6/transitions'
                """
                
                // Comment bhi add karo
                sh """
                    curl -s -X POST \
                    -H 'Authorization: Basic \$(echo -n "${JIRA_USER}:${JIRA_TOKEN}" | base64)' \
                    -H 'Content-Type: application/json' \
                    -d '{"body": {"type": "doc", "version": 1, "content": [{"type": "paragraph", "content": [{"type": "text", "text": "✅ Jenkins Build #${BUILD_NUMBER} SUCCESS! View: ${BUILD_URL}"}]}]}}' \
                    '${JIRA_URL}/rest/api/3/issue/SCRUM-6/comment'
                """
            }
        }
        
        failure {
            script {
                // Issue → In Progress wapas
                sh """
                    curl -s -X POST \
                    -H 'Authorization: Basic \$(echo -n "${JIRA_USER}:${JIRA_TOKEN}" | base64)' \
                    -H 'Content-Type: application/json' \
                    -d '{"transition": {"id": "21"}}' \
                    '${JIRA_URL}/rest/api/3/issue/SCRUM-6/transitions'
                """
                
                // Failure comment
                sh """
                    curl -s -X POST \
                    -H 'Authorization: Basic \$(echo -n "${JIRA_USER}:${JIRA_TOKEN}" | base64)' \
                    -H 'Content-Type: application/json' \
                    -d '{"body": {"type": "doc", "version": 1, "content": [{"type": "paragraph", "content": [{"type": "text", "text": "❌ Jenkins Build #${BUILD_NUMBER} FAILED! Check: ${BUILD_URL}"}]}]}}' \
                    '${JIRA_URL}/rest/api/3/issue/SCRUM-6/comment'
                """
            }
        }
    }
}