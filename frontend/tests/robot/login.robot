*** Settings ***
Library    RequestsLibrary

*** Variables ***
${BACKEND_URL}    http://backend:8000/api/login  

*** Test Cases ***
Login com credenciais válidas
    Create Session    backend    ${BACKEND_URL}
    ${data}=    Create Dictionary    email=admin@email.com    password=123456
    ${response}=    POST On Session    backend    url=${BACKEND_URL}    json=${data}
    Log    ${response.json()}

Login com credenciais inválidas
    Create Session    backend    ${BACKEND_URL}
    ${data}=    Create Dictionary    email=errado@email.com    password=senhaerrada
    ${response}=    POST On Session    backend    url=${BACKEND_URL}    json=${data}
    Log    Status Code: ${response.status_code}
    Log    Response Content: ${response.content}
    Log    Response JSON: ${response.json()}

