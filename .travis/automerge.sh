#!/bin/bash -e

: "${BRANCHES_TO_MERGE_REGEX?}" "${BRANCH_TO_MERGE_INTO?}"
: "${GITHUB_SECRET_TOKEN?}" "${TRAVIS_REPO_SLUG?}"

export GIT_COMMITTER_EMAIL='travis@travis'
export GIT_COMMITTER_NAME='Travis CI'

push_uri="https://$GITHUB_SECRET_TOKEN@github.com/$TRAVIS_REPO_SLUG"

if ! grep -q "$BRANCHES_TO_MERGE_REGEX" <<< "$TRAVIS_BRANCH"; then
    printf "Current branch %s doesn't match regex %s, exiting\\n" \
        "$TRAVIS_BRANCH" "$BRANCHES_TO_MERGE_REGEX" >&2
    exit 0
fi

# Since Travis does a partial checkout, we need to get the whole thing
repo_temp=$(mktemp -d)
git clone "https://github.com/$TRAVIS_REPO_SLUG" "$repo_temp"

# shellcheck disable=SC2164
cd "$repo_temp"

printf 'Checking out %s\n' "$BRANCH_TO_MERGE_INTO" >&2
git checkout "$BRANCH_TO_MERGE_INTO"

printf 'Merging %s\n' "$TRAVIS_COMMIT" >&2
git merge --ff-only "$TRAVIS_COMMIT"

export SEMVER_LAST_TAG=$(git describe --abbrev=0 --tags 2>/dev/null)

if [[ -z $SEMVER_LAST_TAG ]]; then
    >&2 echo "No tags defined creating a new one"
    LAST_COMMIT_DATE=$TODAYS_DATE
    SEMVER_LAST_TAG="0.0.1"
else
    LAST_COMMIT_DATE=$(git log -1 --date=short --pretty=format:%cd $SEMVER_LAST_TAG)
fi

export TODAYS_DATE=$(date +%Y-%m-%d)
# To avoid to many versions use the same tag at a certain day
if [[ $LAST_COMMIT_DATE != $TODAYS_DATE ]]; then
    >&2 echo "Bumping tag"
    SEMVER_NEW_TAG=$($TRAVIS_BUILD_DIR/vendor/bin/semver -v $SEMVER_LAST_TAG -i patch)
else
    >&2 echo "Will use todays patch, just delete it from master. ${SEMVER_LAST_TAG}"
    SEMVER_NEW_TAG=$SEMVER_LAST_TAG
    if [[ $SEMVER_LAST_TAG != "0.0.1" ]]; then
        git push "$push_uri" :refs/tags/$SEMVER_LAST_TAG
    fi
fi

echo "Using tag: $SEMVER_NEW_TAG"
git tag -fa $SEMVER_NEW_TAG &> /dev/null

printf 'Pushing to %s\n' "$TRAVIS_REPO_SLUG" >&2

# Redirect to /dev/null to avoid secret leakage
git push "$push_uri" "$BRANCH_TO_MERGE_INTO" --tags >/dev/null 2>&1

# Deletes the branch
# git push "$push_uri" :"$TRAVIS_BRANCH" >/dev/null 2>&1