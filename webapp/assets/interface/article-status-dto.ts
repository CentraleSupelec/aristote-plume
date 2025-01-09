export interface ArticleStageInfo {
    stage: string;
    total_stages: number;
    stage_number: number;
}

export interface ArticleStatusDto {
    task_id: string;
    task_status: string;
    stage_info: ArticleStageInfo;
}
