#!/bin/sh
source_folder="/var/www/html/videos/"
dest_folder="/var/www/html/public/library/"
folder_cache=""

# Create folder if doesn't exist
create_folder()
{
    # remove source_folder path from the $1 path
    local source_folder_treated=${1#$source_folder}
    # set destination folder
    local folder_name_dest=$dest_folder$source_folder_treated
    folder_cache=$folder_name_dest

    # if folder doesn't exist, we create it
    if [ ! -d "$folder_name_dest" ]; then
        mkdir $folder_name_dest
    fi
}

video_has_no_subtitles()
{
    # return 0 if has subtitles & 1 if it doesn't
    ffmpeg -i $1 -c copy -map 0:s -f null - -v 0 -hide_banner && echo $? || echo $?
}

main() {
    for filename in $(find $source_folder*); do
        # is filename is a dir ?
        if [ -d $filename ]; then
            create_folder $filename
        else
            file_name=$(basename $filename)
            echo "Converting : $echo $filename"
            no_subtitles=$(video_has_no_subtitles $filename)

            if [ $no_subtitles = 1 ]; then
                # convert video and compress it
                ffmpeg -i $filename -vcodec libx264 -crf 18 $folder_cache"/"${file_name%.*}.mp4
            else
                # convert with subtitles
                ffmpeg -i $filename -vf subtitles=$filename -vcodec libx264 -crf 18 $folder_cache"/"${file_name%.*}.mp4
            fi
        fi
    done
}

echo "Run Convert.sh"
main
