https://git-scm.com/download/gui/windows

https://phpjs.ru/2017/02/28/git-%d0%b1%d0%b0%d0%b7%d0%be%d0%b2%d1%8b%d0%b5-%d0%be%d0%bf%d0%b5%d1%80%d0%b0%d1%86%d0%b8%d0%b8/#more-100



git checkout -b task_52219

https://help.github.com/en/articles/adding-an-existing-project-to-github-using-the-command-line
piotr@PIOTRA1C7 Y:\mrbNYoo\modules\feedback_bx
> git credential reject
protocol=https
host=github.com


tree /f > "README.md"
user012018:blayloock012018blayloock012018
blayloock012018@gmail.com/blayloock012018blayloock012018
mrbannyjo@gmail.com/zxcgfH98/1814cf5a00b08f044a8531e013f121153250bbf0 

curl -X POST -d '{"public":true,"files":{"test.txt":{"content":"String file contents"}}}' -u user012018:blayloock012018blayloock012018 https://api.github.com/gists


https://github.com/user012018/tplhtm.git

https://github.com/mrbannyjo/script-for-check-site.git

-----
git checkout -b task_50995

Индексируйте ваши изменения
git add .

Создайте коммит
git commit -m 'Комментарий к ветке task_48283'

И отправьте изменения на удаленный сервер
git push origin task_48283


git push -d <remote_name> <branch_name>
git branch -d <branch_name>

git push -d origin task_52219
git branch -d task_52219

git branch -d task_52219

git reset --hard 71c27777543ccfcb0376dcdd8f6777df055ef479
git push --force

git reset --hard 19f8e72ccdd58a7ce4d0febf4420b273319f3ac5
git push --force


-----


#!/bin/sh
reponame="$1"
if [ "$reponame" = "" ]; then
read -p "Enter Github Repository Name: " reponame
fi
mkdir ./$reponame
cd $reponame
curl -u USERNAME https://api.github.com/user/repos -d "{\"name\":\"$reponame\"}"
git init
echo "ADD README CONTENT" > README.md
git add README.md
git commit -m "Starting Out"  --
git remote add origin git@github.com:USERNAME/$reponame.git
git push -u origin master

git remote set-url origin https://github.com/username/repository.git
git remote set-url origin https://github.com/greetingsgoods/any_good_idea.git
git config -l  // who am?
git config --global user.name "John Doe"
git config --global user.email blololob078@gmail.com
Please make sure you have the correct access rights
git@github.com:greetingsgoods/any_good_idea.git

POST /user/repos
curl -u user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a https://api.github.com/user/repos -d "{\"name\":\"python_regex\"}"
curl -u mrbannyjo:1814cf5a00b08f044a8531e013f121153250bbf0 https://api.github.com/user/repos -d "{\"name\":\"testcatalog\"}"
git init
git commit -m "simple html tpl"
git remote add origin https://github.com/user012018/python_regex.git
git push -u origin master

git add .
git commit -am "python_regex pattern"
$ git push REPOSITORY REFSPEC
# a common example:
# git push origin master

----------is work -http://gearmobile.github.io/git/github-push-and-pull/ -----
git clone git@github.com:gearmobile/arbeit.git
git add .
git commit -m "Continue write article about push and pull in GitHub"  ---OR git checkout entry
git status
git push


---------------
git push origin master --tags

DELETE /repos/:owner/:repo
-https://github.com/user012018/reponame.git
curl -u :username -X "DELETE" https://api.github.com/repos/:username/:repo
curl -X DELETE -H 'Authorization: token {access token goes here}' https://api.github.com/repos/{yourUsername}/{name of repo}

curl -u :user012018 -X DELETE https://api.github.com/repos/:user012018/:reponame
curl https://api.github.com/repos/user012018/reponame -X DELETE -H 'Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a' 

user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a

"github.com/user012018/reponame/settings/delete".

curl -X DELETE -H 'Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a' https://api.github.com/repos/user012018/reponame

blayloock012018@gmail.com/blayloock012018blayloock012018
Username for 'https://github.com': blayloock012018@gmail.com
Password for 'https://blayloock012018@gmail.com@github.com':

ssh-keygen -t rsa -b 4096 -C "your_email@example.com"



------------from github
…or create a new repository on the command line

echo "# usetest" >> README.md
git init
git add README.md
git commit -m "second commit"
git remote add origin https://github.com/user012018/usetest.git
git push -u origin master

…or push an existing repository from the command line
git remote add origin https://github.com/user012018/usetest.git
git push -u origin master
--------except--------
	git remote rm origin
	git remote add origin  https://blayloock012018@gmail.com:blayloock012018blayloock012018@github.com/username/reponame.git

git init
git commit -m "simple html tpl"
git remote add origin https://github.com/user012018/tplhtm.git
git push -u origin master

-------------------------
wget https://api.github.com/user/repos --user=%USER_NAME --password=%PASSWORD --auth-no-challenge --post-data="{\"name\":\"demo\"}"
git remote add origin https://github.com/USER_NAME/demo.git
git push -u origin master

wget https://api.github.com/user/repos --user=%blayloock012018@gmail.com --password=%blayloock012018blayloock012018 --auth-no-challenge --post-data="{\"name\":\"demo\"}"
git remote add origin https://github.com/user012018/demo.git
git push -u origin master

---

curl -u 'USER_NAME' https://api.github.com/user/repos -d'{"name":"demo"}'

curl -u 'user012018' https://api.github.com/user/repos -d'{"name":"demo"}'

'''{"login":"user012018","id":57412720,"node_id":"MDQ6VXNlcjU3NDEyNzIw","avatar_url":"https://avatars2.githubusercontent.com/u/57412720?v=4","gravatar_id":"","url":"https://api.github.com/users/user012018","html_url":"https://github.com/user012018","followers_url":"https://api.github.com/users/user012018/followers","following_url":"https://api.github.com/users/user012018/following{/other_user}","gists_url":"https://api.github.com/users/user012018/gists{/gist_id}","starred_url":"https://api.github.com/users/user012018/starred{/owner}{/repo}","subscriptions_url":"https://api.github.com/users/user012018/subscriptions","organizations_url":"https://api.github.com/users/user012018/orgs","repos_url":"https://api.github.com/users/user012018/repos","events_url":"https://api.github.com/users/user012018/events{/privacy}","received_events_url":"https://api.github.com/users/user012018/received_events","type":"User","site_admin":false,"name":null,"company":null,"blog":"","location":null,"email":null,"hireable":null,"bio":null,"public_repos":3,"public_gists":0,"followers":0,"following":0,"created_at":"2019-11-05T18:14:46Z","updated_at":"2019-11-06T09:34:25Z","private_gists":0,"total_private_repos":0,"owned_private_repos":0,"disk_usage":3,"collaborators":0,"two_factor_authentication":false,"plan":{"name":"free","space":976562499,"collaborators":0,"private_repos":10000}}'''

git remote add origin  https://USERNAME:PASSWORD@github.com/username/reponame.git

git remote add origin  https://user012018:blayloock012018blayloock012018@github.com/user012018/reponame.git

--------------------------
Если ваш ключ SSH уже используется в другом github rep, вы можете создать новый.

2. Создание нового ключа SSH

$ ssh-keygen -t rsa -b 4096 -C "blayloock012018@gmail.com"

3. Добавление ключа на уровне агента SSH

$ eval "$(ssh-agent -s)"
$ ssh-add ~/.ssh/id_rsa_github_user012018

4. Добавьте новый ключ в репозиторий Github.

	> ssh-keygen -t rsa -b 4096 -C "blayloock012018@gmail.com"
	Generating public/private rsa key pair.
	Enter file in which to save the key (/c/Users/Zver/.ssh/id_rsa): id_rsa_github_user012018
	Enter passphrase (empty for no passphrase):
	Enter same passphrase again:
	Your identification has been saved in id_rsa_github_user012018.
	Your public key has been saved in id_rsa_github_user012018.pub.
	The key fingerprint is:
	SHA256:kBOV6vPFS5Ch3NEfmMNFOMRPmpnzoKXZLuW7kDME5/M blayloock012018@gmail.com


ssh -T blayloock012018@gmail.com



-------------------------------
curl -i -H "Authorization: token 5199831f4dd3b79e7c5b7e0ebe75d67aa66e79d4" \
    -d '{ \
        "name": "blog", \
        "auto_init": true, \
        "private": true, \
        "gitignore_template": "nanoc" \
      }' \
    https://api.github.com/user/repos



curl -i -H "Authorization: token 5199831f4dd3b79e7c5b7e0ebe75d67aa66e79d4"     -d '{         "name": "blog",         "auto_init": true,         "private": true,         "gitignore_template": "nanoc"       }'     https://api.github.com/user/repos


curl -i -H "Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a" -d '{"name": "blog","auto_init": true,"private": true,"gitignore_template": "nanoc"   }' https://api.github.com/user/repos

curl -i -H "Authorization: token kBOV6vPFS5Ch3NEfmMNFOMRPmpnzoKXZLuW7kDME5/M" https://api.github.com/user/repos
curl -H "Authorization: token OAUTH-TOKEN" https://api.github.com/users/codertocat -I



curl -H "Authorization: token OAUTH-TOKEN" https://api.github.com/users/codertocat -I

curl -u username https://api.github.com/user
curl -u user012018 https://api.github.com/user
curl -u user012018:oEarldPTiHZClDZLTlPJvX3R3OD6kX4U2TIHAjXiozU https://api.github.com/user

curl -i -H "Authorization: token oEarldPTiHZClDZLTlPJvX3R3OD6kX4U2TIHAjXiozU" https://api.github.com/user/

===Дружим Git с Putty	-https://habr.com/ru/post/217869/
===Создаем токен -https://help.github.com/en/github/authenticating-to-github/creating-a-personal-access-token-for-the-command-line	

> ssh-keygen -t rsa -b 4096 -C "blayloock012018@gmail.com"
Generating public/private rsa key pair.
Enter file in which to save the key (/c/Users/Zver/.ssh/id_rsa): user012018
Enter passphrase (empty for no passphrase):
Enter same passphrase again:
Your identification has been saved in user012018.
Your public key has been saved in user012018.pub.
The key fingerprint is:
SHA256:oEarldPTiHZClDZLTlPJvX3R3OD6kX4U2TIHAjXiozU blayloock012018@gmail.com

curl -u user012018 https://api.github.com/user/repos -d '{"name": "blog1","auto_init": true,"private": true,"gitignore_template": "nanoc"   }' 

curl -u user012018 --data '{"name":"NEW_REPO_NAME"}' https://api.github.com/user/repos

blayloock012018blayloock012018


curl -u user012018 https://api.github.com/user/repos -d '{"name": "Hello-World","description": "This is your first repository","homepage": "https://github.com","private": false,"has_issues": true,"has_projects": true,"has_wiki": true}'


curl -i -H 'Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a' -d '{"name":"csreNewProj"}' https://api.github.com/user/repos


curl -i -H "Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a" -d '{"name": "blog","auto_init": true,"private": true,"gitignore_template": "nanoc"   }' https://api.github.com/user/repos

curl -i -H "Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a" https://api.github.com/user/repos -d '{"name": "Hello-World","description": "This is your first repository","homepage": "https://github.com","private": false,"has_issues": true,"has_projects": true,"has_wiki": true}'

curl -u user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a https://api.github.com/user
curl -u user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a --data '{"name":"NEW_REPO_NAME"}' https://api.github.com/user/repos





curl -u 'user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a' https://api.github.com/user/repos -d '{"name":"sfvdfvdfvdv"}'

curl -u user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a https://api.github.com/user

curl -u user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a https://api.github.com/user/repos -d "{\"name\":\"sfvdfvdfvdv\"}"



curl -u user012018:adca18001c8e5a6aed8a1b419ae0a21cbfe5983a https://postman-echo.com/post --data {"name":"sfvdfvdfvdv"}

curl --request POST --url https://postman-echo.com/post --data 'This is expected to be sent back as part of response body.'

curl -i -H "Authorization: token adca18001c8e5a6aed8a1b419ae0a21cbfe5983a" https://api.github.com/user/repos -d '{"name": "blog", "auto_init": true, "private": true, "gitignore_template": "nanoc"}'




