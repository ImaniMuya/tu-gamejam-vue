# TU Gamejam Vue Rewrite
## Setup
### frontend
```
cd ~
git clone --single-branch --branch frontend https://github.com/ImaniMuya/tu-gamejam-vue.git gj-frontend
cd gj-frontend
cp -r * ../public_html/.
```
### backend
```
cd ../public_html/
git clone --single-branch --branch backend https://github.com/ImaniMuya/tu-gamejam-vue.git server
```
### database
check `server/constants.inc` for sqlite db location and `cd` there.
```
touch current.db
sqlite3 current.db
sqlite> .read ./public_html/server/sql/create-db.sql
```

## Devlopment
pushing to subtrees:
```
git subtree push --prefix client/dist origin frontend
git subtree push --prefix server origin backend
```
